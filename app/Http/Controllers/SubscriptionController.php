<?php

namespace App\Http\Controllers;

use App\Ccbill\CcbillUtils;
use App\Http\Resources\PubnubChannelResource;
use App\Http\Resources\SubscriptionResource;
use App\Logger\Logger;
use App\Mixpanel\Utils\UserProfileUtils;
use App\Models\ActivityLog;
use App\Models\ChannelReadTimetoken;
use App\Models\ConnectionOrazioSession;
use App\Models\Leader;
use App\Models\LeaderCcbillData;
use App\Models\PubnubChannel;
use App\Models\PubnubMessage;
use App\Models\Rookie;
use App\Models\Subscription;
use App\Models\SubscriptionEditHistory;
use App\Models\SubscriptionPackage;
use App\Models\User;
use App\Orazio\OrazioHandler;
use App\Services\Chat\Chat;
use App\Services\Chat\PubNub;
use App\Telegram\TelegramUtils;
use App\Transactions\Morgi\TransactionGift;
use App\Transactions\Morgi\TransactionGiftCoupon;
use App\Utils\ActivityLogsUtils;
use App\Utils\NotificationUtils;
use App\Utils\Subscription\Create\PaidSubscriptionCreateUtils;
use App\Utils\Subscription\Create\SubscriptionCreateUtils;
use App\Utils\Utils;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    public function subscribeOLD(Request $request, Rookie $rookie)
    {
        $validator = Validator::make($request->all(), [
            'subscription_package_id' => ['integer', 'exists:subscription_packages,id', 'required'],
            'photo_id' => ['sometimes', 'exists:photos,id'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        /*
         * Check if leader can subscribe to that rookie
         */
        if (!$rookie->active){
            return response()->json(['message' => 'Rookie is not active', 'type' => 'generic'], 403);
        }

        if ($rookie->hasBlockedLeader(Auth::id())){
            return response()->json(['message' => 'This rookie has blocked you', 'type' => 'generic'], 403);
        }

        $leader = Leader::find(Auth::id());
        if ($leader->blockedRookie($rookie->id)){
            return response()->json(['message' => 'You blocked this rookie', 'type' => 'generic'], 403);
        }

        $subscription_package = SubscriptionPackage::query()->find($request->subscription_package_id);
        $request->merge(['amount' => $subscription_package->amount]);

        $subscription = Subscription::query()->where('leader_id', $leader->id)
            ->where('rookie_id', $rookie->id)
            ->first();

        /*
         * Paid Subscription update
         */
        if(isset($subscription)){

            /*
             * Subscription edit
             */
            if (!isset($subscription->deleted_at)) {

                try {
                    $leader->canEditSubscription($subscription, $request->amount);
                } catch (\Exception $exception) {
                    return response()->json(['message' => 'Unable to update subscription, limit reached', 'type' => $exception->getMessage()], 400);
                }

                DB::beginTransaction();
                try {
                    $this->handleSubscriptionUpdateOLD($request, $subscription, $leader->id, $rookie->id);
                    DB::commit();
                }catch (\Exception $exception){
                    DB::rollBack();
                    return response()->json(['message' => 'Unable to update subscription', 'type' => 'generic'], 400);
                }

                return response()->json(['message' => 'Subscription successfully updated!']);
            }

            /*
             * Subscription reactivate
             */
            try {
                $coupon = $leader->getAvailableCoupon($request->amount);
                $payment_method = LeaderCcbillData::where('leader_id', $leader->id)->orderByDesc('active')->first();

                if(isset($coupon, $payment_method)){

                    $subscription = $leader->reactivateGift($subscription, $request->amount, $payment_method->id);
                    $transaction = $leader->createSubscriptionCouponTransaction($subscription, $coupon->id);
                    $coupon->spend($transaction->id);

                    return response()->json(['message' => trans('subscription.gift_successfully')]);
                }
            }catch (\Exception $exception){
                return response()->json(['message' => $exception->getMessage(), 'type' => 'generic', 'source' => 'coupon_subscription_reactivate'], 400);
            }

            DB::beginTransaction();
            try {
                $result = $leader->attemptPaymentWithPaymentMethods($request->amount, null, Utils::getRealIp($request));
                DB::commit();
            }catch (\Exception $exception){
                DB::rollBack();
                return response()->json(['message' => $exception->getMessage(), 'type' => 'generic', 'source' => 'subscription_reactivate_payment'], 400);
            }

            if($result['status']===false){
                $url = CcbillUtils::jpostSubscription($leader, $rookie->id, $request->amount, $rookie->first_name);
                return response()->json(['url' => $url, 'type' => 'credit_card'], 303);
            }

            DB::beginTransaction();
            try {
                $subscription = $leader->reactivateGift($subscription, $request->amount, $result['payment_method_id']);
                $leader->createSubscriptionTransaction($subscription, Utils::getRealIp($request), $result['subscriptionId'], $result['payment_method_id']);
                DB::commit();
            }catch (\Exception $exception){
                DB::rollBack();
                return response()->json(['message' => $exception->getMessage(), 'type' => 'subscription_reactivate'], 400);
            }

            return response()->json(['message' => trans('subscription.gift_successfully')]);
        }

        /*
         * Try to buy with coupon
         */
        $coupon = $leader->getAvailableCoupon($request->amount);
        $payment_method = LeaderCcbillData::where('leader_id', $leader->id)->orderByDesc('active')->first();
        if(isset($coupon, $payment_method)){

            DB::beginTransaction();
            try {
                $subscription = SubscriptionCreateUtils::configure($leader->id, $rookie->id, $request->amount)
                    ->setLeaderPaymentMethodId($payment_method->id)
                    ->create();
                $transaction = $leader->createSubscriptionCouponTransaction($subscription, $coupon->id);
                $coupon->spend($transaction->id);
                DB::commit();
            }catch (\Exception $exception){
                DB::rollBack();
                return response()->json(['message' => $exception->getMessage(), 'type' => 'generic', 'source' => 'coupon_subscription_create'], 400);
            }

            return response()->json(['message' => trans('subscription.gift_successfully')]);
        }

        /*
         * If leader cannot buy with coupon, lets PAYYYY
         */
        try {
            $leader->canBuyMorgi($request->amount);
        }catch (\Exception $exception){
            return response()->json(['message' => 'You cant gift', 'type' => $exception->getMessage()], 400);
        }

        DB::beginTransaction();
        try {
            $url = PaidSubscriptionCreateUtils::handle($request, $leader, $rookie);
            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            return response()->json(['message' => $exception->getMessage(), 'type' => 'generic', 'source' => 'subscription_create'], 400);
        }

        if(isset($url)){
            return response()->json(['url' => $url, 'type' => 'credit_card'], 303);
        }

        return response()->json(['message' => trans('subscription.gift_successfully')]);
    }

    private function handleSubscriptionUpdateOLD($request, $subscription, $leader_id, $rookie_id)
    {
        try {

            $last_month = strtotime(Carbon::now()->subMonth());
            $field_to_update = [
                'amount' => $request->amount
            ];

            if($subscription->status==='canceled' && isset($subscription->valid_until_at) && strtotime($subscription->valid_until_at)>=$last_month){
                $field_to_update['status'] = 'active';
            }

            if($subscription->status==='active'){

                NotificationUtils::sendNotification($subscription->leader_id, "leader_change_gift_amount", now(),
                    ['ref_user_id' => $subscription->rookie_id, 'old_amount' => $subscription->amount, 'amount' => $request->amount]);

                SubscriptionEditHistory::query()->create([
                    'subscription_id' => $subscription->id, 'old_amount' => $subscription->amount, 'new_amount' => $request->amount
                ]);

                ActivityLog::query()->create([
                    'internal_id' => ActivityLogsUtils::generateInternalId($leader_id), 'rookie_id' => $subscription->rookie_id,
                    'leader_id' => $subscription->leader_id, 'morgi' => $request->amount
                ]);
            }

            $subscription->update($field_to_update);

        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        try {
            Chat::config($leader_id)->startDirectChat(User::find($leader_id), User::find($rookie_id), $subscription->id);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }
    }

    public function indexPackages(Request $request)
    {
        $user = $request->user();
        $leader = Leader::query()->find($user->id);

        $max_morgi_value = (isset($request->is_edit) && $request->is_edit)
            ? $leader->edit_gift_max_morgi
            : $leader->first_gift_max_morgi;

        return SubscriptionPackage::query()
            ->where('dollar_amount', '<=', $max_morgi_value)
            ->get();
    }

    // One click payment broken
    public function store(Request $request, Rookie $rookie): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'subscription_package_id' => ['integer', 'exists:subscription_packages,id', 'required'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        if (!$rookie->active) {
            return response()->json(['message' => 'Rookie is not active', 'type' => 'generic'], 400);
        }

        $leader = Leader::find(Auth::id());

        if ($rookie->hasBlockedLeader($leader->id)) {
            return response()->json(['message' => 'This rookie has blocked you', 'type' => 'generic'], 403);
        }

        if ($leader->blockedRookie($rookie->id)) {
            return response()->json(['message' => 'You blocked this rookie', 'type' => 'generic'], 403);
        }

        $subscription = Subscription::query()->where([
            'leader_id' => $leader->id,
            'rookie_id' => $rookie->id
        ])->first();

        if (isset($subscription)) {
            return response()->json(['message' => "You are already subscribed."], 400);
        }

        $subscription_package = SubscriptionPackage::query()->find($request->subscription_package_id);
        $request->merge(['amount' => $subscription_package->amount]);

        /*
         * Try to buy with coupon
         */
        $coupon = $leader->getAvailableCoupon($subscription_package->amount);
        $payment_method = LeaderCcbillData::where('leader_id', $leader->id)->orderByDesc('active')->first();

        if(isset($coupon, $payment_method)) {
            DB::beginTransaction();
            try {
                $subscription = SubscriptionCreateUtils::configure($leader->id, $rookie->id, $subscription_package->amount)
                    ->setLeaderPaymentMethodId($payment_method->id)
                    ->create();
                $transaction = TransactionGiftCoupon::create(
                    $subscription->rookie_id,
                    $subscription->leader_id,
                    $subscription->amount,
                    $subscription->id,
                    $coupon->id
                );
                $coupon->spend($transaction->id);
                DB::commit();
            }catch (Exception $exception) {
                DB::rollBack();
                Logger::logException($exception);
                return response()->json(['message' => $exception->getMessage(), 'type' => 'generic', 'source' => 'coupon_subscription_create'], 400);
            }

            $response = SubscriptionResource::compute($request, $subscription)->first();

            return response()->json($response, 201);
        }

        /*
         * If leader cannot buy with coupon, lets pay.
         */
        try {
            $leader->canBuyMorgi($subscription_package->amount);
        }catch (Exception $exception){
            return response()->json(['message' => 'You cant gift', 'type' => $exception->getMessage()], 400);
        }

        DB::beginTransaction();
        try {
            $url = PaidSubscriptionCreateUtils::handle($request, $leader, $rookie);
            DB::commit();
        }catch (Exception $exception) {
            DB::rollBack();
            Logger::logException($exception);
            return response()->json(['message' => $exception->getMessage(), 'type' => 'generic', 'source' => 'subscription_create'], 400);
        }

        if(isset($url)) {
            return response()->json(['url' => $url, 'type' => 'credit_card'], 303);
        }

        $response = SubscriptionResource::compute($request, $subscription)->first();

        return response()->json($response, 201);
    }

    public function delete(Rookie $rookie)
    {
        try {
            $leader = Leader::query()->find(Auth::id());
            $leader->ungiftRookie($rookie->id);
        }catch (\Exception $exception){
            Logger::logException($exception);
            return response()->json(['message' => $exception->getMessage()], 500);
        }

        try {
            UserProfileUtils::storeOrUpdate($rookie->id);
            UserProfileUtils::storeOrUpdate($leader->id);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        return response()->json(['message' => trans('subscription.gift_removed')]);
    }

    public function update(Request $request, Subscription $subscription): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'subscription_package_id' => ['integer', 'exists:subscription_packages,id', 'required'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $leader = Leader::find(Auth::id());

        if ($leader->id !== $subscription->leader_id) {
            return response()->json(['message' => "You cannot manage subscriptions that are not yours.", 'type' => 'generic'], 403);
        }

        $rookie = Rookie::find($subscription->rookie_id);

        if (!$rookie->active) {
            return response()->json(['message' => 'Rookie is not active', 'type' => 'generic'], 400);
        }

        if ($rookie->hasBlockedLeader($leader->id)) {
            return response()->json(['message' => 'This rookie has blocked you', 'type' => 'generic'], 403);
        }

        if ($subscription->status !== 'active') {
            return response()->json(['message' => 'The subscription is not actived.', 'type' => 'generic'], 403);
        }

        $subscription_package = SubscriptionPackage::query()->find($request->subscription_package_id);

        if ($subscription_package->amount === $subscription->amount){
            return response()->json(['message' => "The subscription already have same amount"], 400);
        }

        try {
            $leader->canEditSubscription($subscription, $subscription_package->amount);
        } catch (Exception $exception) {
            return response()->json(['message' => 'Unable to update subscription, limit reached', 'type' => $exception->getMessage()], 400);
        }

        DB::beginTransaction();
        try {
            NotificationUtils::sendNotification($subscription->leader_id, "leader_change_gift_amount", now(),
                    ['ref_user_id' => $subscription->rookie_id, 'old_amount' => $subscription->amount, 'amount' => $subscription_package->amount]);
            SubscriptionEditHistory::query()->create([
                    'subscription_id' => $subscription->id, 'old_amount' => $subscription->amount, 'new_amount' => $subscription_package->amount
                ]);
            ActivityLog::create([
                    'internal_id' => ActivityLogsUtils::generateInternalId($leader->id), 'rookie_id' => $subscription->rookie_id,
                    'leader_id' => $subscription->leader_id, 'morgi' => $subscription_package->amount
                ]);
            $subscription->update(['amount' => $subscription_package->amount]);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            Logger::logException($exception);
            return response()->json(['message' => 'Unable to update subscription', 'type' => 'generic'], 400);
        }

        $response = SubscriptionResource::compute($request, $subscription)->first();

        return response()->json($response);
    }

    public function reactivate(Request $request, Subscription $subscription): JsonResponse
    {
        $leader = Leader::find(Auth::id());

        if ($leader->id !== $subscription->leader_id) {
            return response()->json(['message' => "You cannot manage subscriptions that are not yours.", 'type' => 'generic'], 403);
        }

        $rookie = Rookie::find($subscription->rookie_id);

        if (!$rookie->active) {
            return response()->json(['message' => 'Rookie is not active', 'type' => 'generic'], 400);
        }

        if ($rookie->hasBlockedLeader($leader->id)) {
            return response()->json(['message' => 'This rookie has blocked you', 'type' => 'generic'], 403);
        }

        if($subscription->status !== 'canceled') {
            return response()->json(['message' => 'Subscription must be canceled.'], 400);
        }

        if (!(isset($subscription->valid_until_at) && $subscription->valid_until_at >= Carbon::now())){
            return response()->json(['message' => 'Subscription has expired.'], 403);
        }

        $subscription->update(['status' => 'active']);

        $response = SubscriptionResource::compute($request, $subscription)->first();

        return response()->json($response);
    }

    public function renew(Request $request, Subscription $subscription): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'subscription_package_id' => ['integer', 'exists:subscription_packages,id', 'required'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $leader = Leader::find(Auth::id());

        if ($leader->id !== $subscription->leader_id) {
            return response()->json(['message' => "You cannot manage subscriptions that are not yours.", 'type' => 'generic'], 403);
        }

        $rookie = Rookie::find($subscription->rookie_id);

        if (!$rookie->active) {
            return response()->json(['message' => 'Rookie is not active', 'type' => 'generic'], 400);
        }

        if ($rookie->hasBlockedLeader($leader->id)) {
            return response()->json(['message' => 'This rookie has blocked you', 'type' => 'generic'], 403);
        }

        if ($subscription->status === 'active') {
            return response()->json(['message' => 'This subscription is already active.'], 403);
        }

        if($subscription->status === 'canceled' && isset($subscription->valid_until_at) && $subscription->valid_until_at >= Carbon::now()) {
            return response()->json(['message' => 'Subscription already valid, try to reactivate'], 400);
        }

        /*
         * Try to buy with coupon
         */
        $subscription_package = SubscriptionPackage::query()->find($request->subscription_package_id);
        $coupon = $leader->getAvailableCoupon($subscription_package->amount);
        $payment_method = LeaderCcbillData::where('leader_id', $leader->id)->orderByDesc('active')->first();

        if(isset($coupon, $payment_method)) {
            DB::beginTransaction();
            try {
                $subscription = $leader->reactivateGift($subscription, $subscription_package->amount, $payment_method->id);
                $transaction = TransactionGiftCoupon::create(
                    $subscription->rookie_id,
                    $subscription->leader_id,
                    $subscription->amount,
                    $subscription->id,
                    $coupon->id
                );
                $coupon->spend($transaction->id);
                DB::commit();
            }catch (\Exception $exception) {
                DB::rollBack();
                Logger::logException($exception);
                return response()->json(['message' => $exception->getMessage(), 'type' => 'generic', 'source' => 'coupon_subscription_reactivate'], 400);
            }

            $response = SubscriptionResource::compute($request, $subscription)->first();

            return response()->json($response);
        }

        /*
         * If leader cannot buy with coupon, lets pay.
         */
        DB::beginTransaction();
        try {
            $result = $leader->attemptPaymentWithPaymentMethods($subscription_package->amount, null, Utils::getRealIp($request));
            DB::commit();
        }catch (\Exception $exception) {
            DB::rollBack();
            Logger::logException($exception);
            return response()->json(['message' => $exception->getMessage(), 'type' => 'generic', 'source' => 'subscription_reactivate_payment'], 400);
        }

        if($result['status']===false) {
            $url = CcbillUtils::jpostSubscription($leader, $rookie->id, $subscription_package->amount, $rookie->first_name);
            return response()->json(['url' => $url, 'type' => 'credit_card'], 303);
        }

        DB::beginTransaction();
        try {
            $subscription = $leader->reactivateGift($subscription, $subscription_package->amount, $result['payment_method_id']);
            TransactionGift::create(
                $subscription->rookie_id,
                $subscription->leader_id,
                $subscription->amount,
                $subscription->id,
                $result['payment_method_id'],
                $result['subscriptionId'],
                false,
                Utils::getRealIp($request)
            );
            DB::commit();
        }catch (\Exception $exception) {
            DB::rollBack();
            Logger::logException($exception);
            return response()->json(['message' => $exception->getMessage(), 'type' => 'subscription_reactivate'], 400);
        }

        $response = SubscriptionResource::compute($request, $subscription)->first();

        return response()->json($response);
    }
}
