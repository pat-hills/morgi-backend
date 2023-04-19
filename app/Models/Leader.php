<?php

namespace App\Models;

use App\Enums\PubnubBroadcastEnum;
use App\Logger\Logger;
use App\Transactions\Morgi\TransactionGift;
use Carbon\Carbon;
use App\Utils\Utils;
use App\Ccbill\CcbillUtils;
use App\Mixpanel\Events\EventCreditCardError;
use App\Mixpanel\Events\EventInsertedCreditCard;
use App\Orazio\OrazioHandler;
use App\Services\Chat\Chat;
use App\Ccbill\CcbillFormUtils;
use App\Telegram\TelegramUtils;
use App\Utils\TransactionUtils;
use App\Transactions\MicroMorgi\TransactionLeaderMicromorgiBonus;
use App\Transactions\Morgi\TransactionRookieMorgiBonus;
use App\Utils\NotificationUtils;
use App\Utils\SubscriptionUtils;
use App\Ccbill\CcbillCurrencyCodes;
use Illuminate\Database\Eloquent\Model;
use App\Utils\Pubnub\PubnubBroadcastUtils;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Leader extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'interested_in_gender_id',
        'micro_morgi_balance',
        'first_rookie',
        'has_approved_transaction',
        'user_id',
        'spender_group_id',
        'seen_first_rookie',
        'spender_group_forced_by_admin',
        'global_id',
        'internal_status',
        'total_coupons_got',
        'orazio_sessions_count',
        'has_converter_chat',
        'carousel_type'
    ];

    protected $casts = [
        'seen_first_rookie' => 'boolean',
        'spender_group_forced_by_admin' => 'boolean',
        'has_approved_transaction' => 'boolean',
        'has_converter_chat' => 'boolean'
    ];

    public function savedGoals()
    {
        return $this->belongsToMany(Goal::class, 'saved_goals', 'leader_id', 'goal_id');
    }

    //TODO: fare na resource
    public function getCouponsAttribute()
    {
        $response = [];

        $coupons = Coupon::query()
            ->where('is_spent', false)
            ->where('leader_id', $this->id)
            ->get();

        foreach ($coupons as $coupon){

            $currency = (int)$coupon->currency_value;
            if(!array_key_exists($currency, $response)){
                $response[$currency] = [
                    'currency_value' => $currency,
                    'count' => 1
                ];
                continue;
            }

            $response[$currency]['count'] += 1;
        }

       return array_values($response);
    }

    public function getMorgiGivenToRookie(int $rookie_id)
    {
        return Transaction::query()
            ->where('leader_id', $this->id)
            ->where('rookie_id', $rookie_id)
            ->where('type', 'gift')
            ->whereNull('refund_type')
            ->whereNotNull('morgi')
            ->sum('morgi');
    }

    public function getMicroMorgiGivenToRookie(int $rookie_id)
    {
        return Transaction::query()
            ->where('leader_id', $this->id)
            ->where('rookie_id', $rookie_id)
            ->where('type', 'chat')
            ->whereNull('refund_type')
            ->whereNotNull('micromorgi')
            ->sum('micromorgi');
    }

    public function isBlockedFromRookie(int $rookie_id): bool
    {
        return UserBlock::where('to_user_id', $this->id)
            ->where('from_user_id', $rookie_id)
            ->whereNull('deleted_at')
            ->exists();
    }

    public function blockedRookie(int $rookie_id): bool
    {
        return UserBlock::where('to_user_id', $rookie_id)
            ->where('from_user_id', $this->id)
            ->whereNull('deleted_at')
            ->exists();
    }

    public function hasPaymentMethod(): bool
    {
        return LeaderCcbillData::where('leader_id', $this->id)
            ->where('active', true)
            ->exists();
    }

    public function getPaymentMethods()
    {
        return LeaderCcbillData::where('leader_id', $this->id)
            ->where('active', true)
            ->get();
    }

    public function hasPath(int $path_id): bool
    {
        return UserPath::where('user_id', $this->id)->where('path_id', $path_id)->exists();
    }

    public function unlockPath($path_id)
    {
        if($this->hasPath($path_id)){
            return;
        }

        UserPath::create([
            'user_id' => $this->id,
            'path_id' => $path_id
        ]);

        $this->setMainPath($path_id, 'subscription');
    }

    public function getSpenderGroupAttribute()
    {
        return SpenderGroup::find($this->spender_group_id);
    }

    public function popMicromorgi($amount_micromorgi)
    {
        if($this->micro_morgi_balance===0){
            return;
        }

        if(($this->micro_morgi_balance-$amount_micromorgi)<0){
            $amount_micromorgi = $this->micro_morgi_balance;
        }

        $this->update(['micro_morgi_balance' => $this->micro_morgi_balance - $amount_micromorgi]);
    }

    public function attemptPaymentWithPaymentMethods($dollar_amount, $micromorgi_amount = null, $ip_address = null)
    {
        if(!$this->hasPaymentMethod()){
            return ['status' => false];
        }

        $payment_methods = $this->getPaymentMethods();

        foreach($payment_methods as $payment_method){

            try {
                $transaction = CcbillUtils::oneTimeTransaction($payment_method, $dollar_amount, !isset($micromorgi_amount), $ip_address);
            }catch (\Exception $exception){
                throw new \Exception($exception->getMessage());
            }

            if($transaction['status']===true){
                $payment_method->update(['active' => true]);
                return ['status' => true, 'payment_method_id' => $payment_method->id, 'subscriptionId' => $transaction['subscriptionId']];
            }

            $payment_method->update(['active' => false]);
            try {
                EventCreditCardError::config($this->id, $dollar_amount);
            }catch (\Exception $exception){
            }
        }

        return ['status' => false];
    }

    public function addCreditCard($update_subscriptions_ids = null)
    {
        $leader_user = User::find($this->id);
        $currency = CcbillCurrencyCodes::getCurrencyCode($leader_user->currency);

        try {
            return CcbillFormUtils::createCreditCardForm($this->id, $currency, $update_subscriptions_ids);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function attemptSubscriptionsRenew($subscriptions, $need_ccbill = false, $is_apply = false, $ip_address = null)
    {
        if(!$this->hasPaymentMethod() || ($need_ccbill && !$is_apply)){
            return ['status' => false];
        }

        $payment_method = LeaderCcbillData::where('leader_id', $this->id)
            ->where('active', true)
            ->latest()
            ->first();

        $total_amount = $subscriptions->sum('amount');

        try {
            $transaction = CcbillUtils::oneTimeTransaction($payment_method, $total_amount, true, $ip_address);
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        if($transaction['status']===true){
            $payment_method->update(['active' => true]);
            return ['status' => true, 'payment_method_id' => $payment_method->id, 'subscriptionId' => $transaction['subscriptionId']];
        }

        $payment_method->update(['active' => false]);
        try {
            EventCreditCardError::config($this->id, $total_amount);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        return ['status' => false];
    }

    public function getGiveback($total_subscriptions_count)
    {
        $givebacks = Giveback::query()->get();
        $latest_giveback = $givebacks->sortByDesc('total_subscriptions_count')->first();

        if($total_subscriptions_count>=$latest_giveback->total_subscriptions_count){
            return $latest_giveback->micromorgi;
        }

        $giveback = $givebacks->where('total_subscriptions_count', $total_subscriptions_count)->first();

        return (isset($giveback)) ? $giveback->micromorgi : null;
    }

    public function sendGiveback(int $micromorgi)
    {
        TransactionLeaderMicromorgiBonus::create(
            $this->id,
            $micromorgi,
            null,
            'Morgi appreciates you!'
        );
    }

    public function reactivateGift($subscription, $amount, $leader_payment_method_id)
    {
        if(!isset($subscription->deleted_at) || $subscription->status !== 'canceled'){
            throw new \Exception("Subscription already active");
        }

        $rookie_id = $subscription->rookie_id;
        $rookie_user = User::find($rookie_id);
        $leader_user = User::find($this->id);

        try {
            $rookie_path = UserPath::query()->where('user_id', $rookie_id)->where('is_subpath', false)->first();
            if(isset($rookie_path) && !$this->hasPath($rookie_path->path_id)){
                $this->unlockPath($rookie_path->path_id);
            }

            $action_tracking = $this->retrieveOrCreateActionTracking($rookie_id);
            $action_tracking->update(['paid_rookie' => true]);

            $next_donation_at = SubscriptionUtils::computeNextDonationAt(now()->toDateTimeString(), now()->toDateTimeString());
            $subscription->update([
                'status' => 'active',
                'amount' => $amount,
                'leader_payment_method_id' => $leader_payment_method_id,
                'last_subscription_at' => now(),
                'next_donation_at' => $next_donation_at,
                'valid_until_at' => $next_donation_at,
                'deleted_at' => null
            ]);

            $micromorgi_giveback = $this->getGiveback($leader_user->total_subscriptions_count);
            if(isset($micromorgi_giveback)){
                $this->sendGiveback($micromorgi_giveback);
            }

            if(isset($leader_user->referred_by) && !isset($leader_user->referral_bonus_transaction_id)){

                $rookie_referral = User::query()->find($leader_user->referred_by);
                if(isset($rookie_referral) && $rookie_referral->active){
                    $referral_transaction = TransactionRookieMorgiBonus::create(
                        $rookie_referral->id,
                        10,
                        'For a Morgi Friend who has gifted Morgis'
                    );
                    $leader_user->update(['referral_bonus_transaction_id' => $referral_transaction->id]);
                }
            }

            NotificationUtils::sendNotification($subscription->leader_id, "leader_new_gift", now(),
                ['ref_user_id' => $subscription->rookie_id, 'amount_morgi' => $subscription->amount]);

            if(isset($micromorgi_giveback)){
                NotificationUtils::sendNotification($subscription->leader_id, "giveback", now(), [
                    'reason' => $leader_user->total_subscriptions_count . Utils::getNumberNumerals($leader_user->total_subscriptions_count),
                    'amount_micromorgi' => $micromorgi_giveback
                ]);
            }

            NotificationUtils::sendNotification($subscription->rookie_id, "rookie_new_gift", now(),
                ['ref_user_id' => $subscription->leader_id, 'amount_morgi' => $subscription->amount]);

        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        try {
            $channel = Chat::config($leader_user->id)->startDirectChat($leader_user, $rookie_user, $subscription->id);
        } catch (\Exception $e) {
        }

        if(isset($rookie_user->joined_telegram_bot_at) && isset($channel)){
            $leader_user = User::query()->find($this->id);
            TelegramUtils::sendTelegramNotifications(
                $rookie_user->telegram_chat_id,
                'first_gift',
                [
                    'leader_username' => $leader_user->username,
                    'amount' => $amount,
                    'channel_name' => $channel->name
                ],
                $rookie_user->id
            );
        }

        try {
            OrazioHandler::freshSeen($this->id, 'Reactivated subscription');
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        return $subscription;
    }

    public function createSubscriptionTransaction($subscription,
                                                  $ip_address,
                                                  $ccbill_subscriptionId,
                                                  $payment_method_id,
                                                  $paused_channel_exists = false)
    {
        try {
            $transaction = TransactionGift::create(
                $subscription->rookie_id,
                $subscription->leader_id,
                $subscription->amount,
                $subscription->id,
                $payment_method_id,
                $ccbill_subscriptionId,
                $paused_channel_exists,
                $ip_address
            );

            $pubnub_channel = PubnubChannel::where('rookie_id', $subscription->rookie_id)->where('leader_id', $this->id)->first();
            $type = ($paused_channel_exists) ? PubnubBroadcastEnum::TYPE_GIFT_AFTER_PAUSE_TRANSACTION : PubnubBroadcastEnum::TYPE_GIFT_TRANSACTION;
            PubnubBroadcastUtils::config($pubnub_channel, $type)
                ->setLeaderId($this->id)
                ->setSenderId($this->id)
                ->setRookieId($subscription->rookie_id)
                ->setTransactionId($transaction->id)
                ->send();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        return $transaction;
    }

    public function createOrUpdateFailedTransaction($subscription, $reason = null, $code = null)
    {
        $failed_transaction = TransactionFailed::where('subscription_id', $subscription->id)->first();

        $is_first_subscription_transaction = !LeaderPayment::where('subscription_id', $subscription->id)->where('status', 'paid')->exists();
        $leader_payment_type = ($is_first_subscription_transaction) ? 'first_purchase' : 'rebill';

        LeaderPayment::query()->create([
            'status' => 'failed', 'leader_id' => $subscription->leader_id, 'currency_type' => 'morgi',
            'amount' => $subscription->amount, 'dollar_amount' => $subscription->amount, 'ccbill_failureReason' => $reason,
            'subscription_id' => $subscription->id, 'type' => $leader_payment_type, 'ccbill_failureCode' => $code
        ]);

        TransactionFailedHistory::query()->create([
            'subscription_id' => $subscription->id,
            'leader_payment_method_id' => $subscription->leader_payment_method_id,
            'reason' => $reason, 'amount' => $subscription->amount
        ]);

        if(isset($failed_transaction)){

            $subscription->update(['failed_at' => now()]);
            $failed_transaction->update(['attempts' => $failed_transaction->attempts+1, 'last_attempt_at' => Carbon::now()]);
            return;
        }

        TransactionFailed::create(['subscription_id' => $subscription->id, 'last_attempt_at' => Carbon::now()]);
    }

    public function hasNewCreditCard()
    {
        $leader_payment_method = LeaderCcbillData::where('leader_id', $this->id)
            ->where('active', true)
            ->latest()
            ->first();

        if(!isset($leader_payment_method)){
            return false;
        }

        return Subscription::query()
            ->join('users', 'users.id', '=', 'subscriptions.rookie_id')
            ->leftJoin('leaders_ccbill_data', 'leaders_ccbill_data.id', '=', 'subscriptions.leader_payment_method_id')
            ->where('subscriptions.leader_id', $this->id)
            ->where('users.active', true)
            ->whereIn('subscriptions.status', ['active', 'failed'])
            ->where('subscriptions.type', '=', 'paid')
            ->where('subscriptions.leader_payment_method_id', '!=', $leader_payment_method->id)
            ->exists();
    }

    public function getMorgiMonthlyPrevisionAttribute()
    {
        $subscriptions = Subscription::query()
            ->where('leader_id', $this->id)
            ->whereIn('status', ['active', 'canceled', 'failed_attempt'])
            ->get();

        $subscriptions_ids = $subscriptions->pluck('id')->toArray();
        $subscriptions_prevision = $subscriptions->sum('amount');

        $transactions_sum = LeaderPayment::query()
            ->where('leader_id', $this->id)
            ->where('status', 'paid')
            ->where('currency_type', 'morgi')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereNotIn('subscription_id', $subscriptions_ids)
            ->sum('dollar_amount');

        return $subscriptions_prevision + $transactions_sum;
    }

    public function getMicroMorgiMonthlyPrevisionAttribute()
    {
        return LeaderPayment::query()
            ->where('leader_id', $this->id)
            ->where('status', 'paid')
            ->where('currency_type', 'micro_morgi')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('dollar_amount');
    }

    public function getMicroMorgiDailyPrevisionAttribute()
    {
        return LeaderPayment::query()
            ->where('leader_id', $this->id)
            ->where('status', 'paid')
            ->where('currency_type', 'micro_morgi')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereDay('created_at', Carbon::now()->day)
            ->sum('dollar_amount');
    }

    public function createPaymentMethod($request, $is_active)
    {
        $leader_payment_method_attributes = $request->only(
            'subscriptionId', 'clientAccnum', 'clientSubacc', 'subscriptionCurrencyCode', 'cardType',
            'last4', 'expDate', 'paymentAccount', 'ipAddress', 'reservationId', 'leader_id', 'error', 'active',
            'accountingCurrencyCode', 'address1', 'billedCurrencyCode', 'billedInitialPrice',
            'billedRecurringPrice', 'bin', 'city', 'dynamicPricingValidationDigest', 'email', 'firstName',
            'lastName', 'formName', 'initialPeriod', 'paymentType', 'postalCode', 'priceDescription',
            'referringUrl', 'state', 'subscriptionTypeId', 'subscriptionInitialPrice', 'transactionId'
        );

        $leader_payment_method_attributes['active'] = $is_active;
        $leader_payment_method_attributes['leader_id'] = $this->id;
        $leader_payment_method_attributes['billingCountry'] = $request->country;

        $dollars_amount = (isset($request->metadata, $request->metadata->dollar_amount))
            ? $request->metadata->dollar_amount
            : 0;

        try {
            EventInsertedCreditCard::config($this->id, $dollars_amount);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        return LeaderCcbillData::query()->create($leader_payment_method_attributes);
    }

    public function getMonthlyMorgiLimitAttribute()
    {
        return SpenderGroup::find($this->spender_group_id)->monthly_morgi_spent;
    }

    public function getMonthlyMicroMorgiLimitAttribute()
    {
        return SpenderGroup::find($this->spender_group_id)->limit_monthly_micromorgi;
    }

    public function getDailyMicroMorgiLimitAttribute()
    {
        return SpenderGroup::find($this->spender_group_id)->limit_daily_micromorgi;
    }

    public function getFirstGiftMaxMorgiAttribute()
    {
        return SpenderGroup::find($this->spender_group_id)->morgi_per_new_rookie;
    }

    public function getEditGiftMaxMorgiAttribute()
    {
        return SpenderGroup::find($this->spender_group_id)->edit_morgi;
    }

    public function createLeaderStatusHistory($new_status, $changed_by, $reason = null)
    {
        $old_status = $this->internal_status ?? User::find($this->id)->status;

        if($old_status!=$new_status){
            UserStatusHistory::create([
                'user_id' => $this->id,
                'old_status' => $old_status,
                'new_status' => strtolower(str_replace('_', ' ', $new_status)),
                'changed_by' => $changed_by,
                'reason' => $reason
            ]);
        }
    }

    private function canBuy()
    {
        if($this->internal_status==='suspend'){
            throw new \Exception('suspend');
        }

        if($this->internal_status==='under_review'){
            throw new \Exception('under_review');
        }

        $user = User::query()->select('status')->find($this->id);
        if(!isset($user)){
            return;
        }

        $user_status = $user->status;

        if($user_status==='fraud'){
            throw new \Exception('fraud');
        }

        if($user_status==='blocked'){
            throw new \Exception('blocked');
        }
    }

    public function canBuyMorgi($amount)
    {
        try {
            $this->canBuy();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        $prevision =  $this->morgi_monthly_prevision;
        if(($prevision + $amount) > $this->monthly_morgi_limit){
            throw new \Exception('morgi_limit');
        }
    }

    public function canEditSubscription($subscription, $amount)
    {
        try {
            $this->canBuy();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        $prevision = $this->morgi_monthly_prevision - $subscription->amount;
        if(($prevision + $amount) > $this->monthly_morgi_limit){
            throw new \Exception('morgi_limit');
        }
    }

    public function canBuyMicroMorgi($price)
    {
        try {
            $this->canBuy();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        if(($this->micro_morgi_monthly_prevision + $price) > $this->monthly_micro_morgi_limit){
            throw new \Exception('monthly_micromorgi_limit');
        }

        if(($this->micro_morgi_daily_prevision + $price) > $this->daily_micro_morgi_limit){
            throw new \Exception('daily_micromorgi_limit');
        }
    }

    public function setMainPath(int $path_id, string $source): void
    {
        /*
         * Lets remove old main path
         */
        LeaderPath::query()->where('leader_id', $this->id)
            ->update(['is_main' => false]);

        /*
         * Create new main path or set an older to new
         */
        $leader_path = LeaderPath::query()->where('leader_id', $this->id)
            ->where('path_id', $path_id)
            ->first();

        if(isset($leader_path)){
            $leader_path->update(['source' => $source, 'is_main' => true]);
            return;
        }

        LeaderPath::query()->create([
            'leader_id' => $this->id,
            'path_id' => $path_id,
            'is_main' => true,
            'source' => $source
        ]);

        if($source !== 'subscription'){
            try {
                OrazioHandler::freshSeen($this->id, 'New main path',true);
            }catch (\Exception $exception){
            }
        }
    }

    public function addFilteredPath(int $path_id): void
    {
        $path = Path::query()
            ->where('id', $path_id)
            ->where('is_subpath', false)
            ->first();

        if (!isset($path)) {
            return;
        }

        LeaderPathFilter::query()->create(['leader_id' => $this->id, 'path_id' => $path_id]);
        $path_filters_count = LeaderPathFilter::query()->where('leader_id', $this->id)
            ->where('path_id', $path_id)
            ->whereDate('created_at', '>=', Carbon::now()->subDays(2)->toDateString())
            ->count();

        if($path_filters_count){
            $this->setMainPath($path_id, 'filter');
        }
    }

    public function retrieveOrCreateActionTracking(int $rookie_id): ActionTracking
    {
        $action_tracking = ActionTracking::query()->where('leader_id', $this->id)
            ->where('rookie_id', $rookie_id)
            ->first();

        if(!isset($action_tracking)){
            $action_tracking = ActionTracking::query()->create([
                'leader_id' => $this->id,
                'rookie_id' => $rookie_id
            ]);
        }

        return $action_tracking;
    }

    public function ungiftRookie(int $rookie_id, int $ended_by = null): void
    {
        $subscription = Subscription::query()
            ->where('leader_id', $this->id)
            ->where('rookie_id', $rookie_id)
            ->first();

        if(!isset($subscription)){
            return;
        }

        try {
            $subscription->update([
                'status' => 'canceled',
                'canceled_at' => now(),
                'ended_by' => $ended_by
            ]);
            PubnubChannel::where('subscription_id', $subscription->id)->update(['active' => false]);
        }catch (\Exception $exception){
            throw new \Exception("Error during the delete of the gift");
        }
    }

    public function endSubscription(int $rookie_id, int $ended_by = null): void
    {
        $subscription = Subscription::query()
            ->where('leader_id', $this->id)
            ->where('rookie_id', $rookie_id)
            ->first();

        if(!isset($subscription)){
            return;
        }

        try {
            $subscription->update([
                'status' => 'canceled',
                'canceled_at' => now(),
                'ended_by' => $ended_by,
                'sent_reply_reminder_email_at' => null,
                'deleted_at' => now(),
                'valid_until_at' => now()
            ]);
            PubnubChannel::where('subscription_id', $subscription->id)->update(['active' => false]);
        }catch (\Exception $exception){
            throw new \Exception("Error during the delete of the gift");
        }
    }

    public function getAvailableCoupon(float $morgi_amount): ?Coupon
    {
        return Coupon::where('leader_id', $this->id)
            ->where('currency_value', $morgi_amount)
            ->where('is_spent', false)
            ->first();
    }

    // Not enough time to figure out a nice solution
    public function nickname()
    {
        return $this->hasMany(Nickname::class, 'nicknamed_id');
    }
}
