<?php

namespace App\Http\Controllers;

use App\Ccbill\CcbillCurrencyCodes;
use App\Ccbill\CcbillFormUtils;
use App\Ccbill\CcbillUtils;
use App\Http\Resources\SubscriptionResource;
use App\Logger\Logger;
use App\Models\Leader;
use App\Models\LeaderCcbillData;
use App\Models\Subscription;
use App\Transactions\Morgi\TransactionGift;
use App\Utils\SubscriptionRenewUtils;
use App\Utils\SubscriptionUtils;
use App\Utils\Utils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CcbillController extends Controller
{
    public function indexToRenew(Request $request): JsonResponse
    {
        $user = $request->user();

        $subscriptions = Subscription::query()->select('subscriptions.*')
            ->selectRaw("subscriptions.status, subscriptions.amount, subscriptions.failed_at, subscriptions.id, subscriptions.rookie_id, subscriptions.leader_id")
            ->join('users', 'users.id', '=', 'subscriptions.rookie_id')
            ->where('subscriptions.leader_id', $user->id)
            ->where('users.active', true)
            ->where('subscriptions.type', '=', 'paid')
            ->groupBy('subscriptions.id')
            ->get();

        $subscriptions = SubscriptionResource::compute($request, $subscriptions)->get();

        return response()->json([
            'errors' => $subscriptions->whereIn('status', ['unsufficent_funds', 'failed'])->values(),
            'subscriptions' => $subscriptions->where('status', 'active')->values()
        ]);
    }

    public function indexToRenewWithCc(Request $request): JsonResponse
    {
        $user = $request->user();

        $leader_payment_method = LeaderCcbillData::where('leader_id', $user->id)
            ->where('active', true)
            ->latest()
            ->first();

        if(!isset($leader_payment_method)){
            return response()->json(['message' => "You don't have an active credit card"], 400);
        }

        $subs = Subscription::query()->select('subscriptions.*')
            ->join('users', 'users.id', '=', 'subscriptions.rookie_id')
            ->where('subscriptions.leader_id', $user->id)
            ->where('subscriptions.type', '=', 'paid')
            ->where('users.active', true)
            ->where('subscriptions.leader_payment_method_id', '!=', $leader_payment_method->id)
            ->groupBy('subscriptions.id')
            ->get();

        $subscriptions_resource = SubscriptionResource::compute(
            $request,
            $subs,
            'extended'
        )->get();

        $errors_with_card = [];
        $subscriptions_with_card = [];

        $errors = $subscriptions_resource->whereIn('status', ['unsufficent_funds', 'failed'])->values();

        foreach ($errors as $error){
            $errors_with_card[$error->last4][] = $error;
        }

        $subscriptions = $subscriptions_resource->where('status', 'active')->values();

        foreach ($subscriptions as $subscription){
            $subscriptions_with_card[$subscription->last4][] = $subscription;
        }

        $response_errors = [];
        $response_subscriptions = [];

        foreach ($errors_with_card as $cc => $error_with_card){
            $response_errors[] = [
                'credit_card' => ['last4' => $cc],
                'data' => $error_with_card
            ];
        }

        foreach ($subscriptions_with_card as $cc => $subscription_with_card){
            $response_subscriptions[] = [
                'credit_card' => ['last4' => $cc],
                'data' => $subscription_with_card
            ];
        }

        return response()->json([
            'errors' => $response_errors,
            'subscriptions' => $response_subscriptions
        ]);
    }

    public function addCreditCard(Request $request, $update_subscriptions_ids = null)
    {
        $user = $request->user();
        if(!isset($user)){
            return response()->json(['message' => "Error during the creation of the form"], 400);
        }

        $currency = CcbillCurrencyCodes::getCurrencyCode($user->currency);

        try {
            $url = CcbillFormUtils::createCreditCardForm($user->id, $currency, $update_subscriptions_ids);
        } catch (\Exception $exception) {
            Logger::logException($exception);
            return response()->json(['message' => "Error during the creation of the form"], 400);
        }

        return response()->json(['url' => $url], 303);
    }

    public function renewSubscriptions(Request $request)
    {
        $actions = SubscriptionRenewUtils::ACTIONS;

        $validator = Validator::make($request->all(), [
            'errors' => ['sometimes', 'array', 'nullable'],
            'errors.*.id' => ['sometimes', 'integer', 'exists:subscriptions,id'],
            'errors.*.action' => ['sometimes', 'string', Rule::in($actions)],
            'subscriptions' => ['sometimes', 'array', 'nullable'],
            'subscriptions.*.id' => ['sometimes', 'integer', 'exists:subscriptions,id'],
            'subscriptions.*.action' => ['sometimes', 'string', Rule::in($actions)],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $leader_user = $request->user();
        $leader = Leader::find($leader_user->id);

        $builder = SubscriptionRenewUtils::config(
            $leader_user,
            $request->get('subscriptions') ?? [],
            $request->get('errors') ?? []
        );

        $subscriptions_ids_with_errors_to_update = $builder->subscriptions_ids_with_errors_to_update;
        $active_subscriptions_ids_to_update = $builder->active_subscriptions_ids_to_update;

        if(empty($subscriptions_ids_with_errors_to_update) && empty($active_subscriptions_ids_to_update)){
            return response()->json();
        }

        $subscriptions_with_errors_to_update = Subscription::query()->select('subscriptions.*')
            ->join('users', 'users.id', '=', 'subscriptions.rookie_id')
            ->where('subscriptions.leader_id', $leader->id)
            ->whereIn('subscriptions.id', $subscriptions_ids_with_errors_to_update)
            ->where('subscriptions.type', '=', 'paid')
            ->whereIn('subscriptions.status', ['unsufficent_funds', 'failed'])
            ->where('users.active', true)
            ->groupBy('subscriptions.id')
            ->get();

        /*
         * This works for the popup that apply a new credit card to active subscriptions
         */
        if(!((isset($request->type) && $request->type === 'apply_card') || $subscriptions_with_errors_to_update->count() > 0)){
            DB::beginTransaction();
            try {
                $url = $leader->addCreditCard($active_subscriptions_ids_to_update);
                DB::commit();
            }catch (\Exception $exception){
                DB::rollBack();
                Logger::logException($exception);
                return response()->json(['message' => "CCBill is unavailable", 'error' => $exception->getMessage()], 400);
            }
            return response()->json(['url' => $url], 303);
        }

        /*
         * This try to renew subscriptions with errors.
         * If renewal go fine, reactivate subscriptions with errors and update active subscriptions
         */
        DB::beginTransaction();
        try {
            $result = $leader->attemptSubscriptionsRenew(
                $subscriptions_with_errors_to_update,
                isset($request->need_ccbill),
                (isset($request->type) && $request->type === 'apply_card'),
                Utils::getRealIp($request)
            );
            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            Logger::logException($exception);
            return response()->json(['message' => "CCBill is unavailable", 'error' => $exception->getMessage()], 400);
        }

        /*
         * Handle the failure of the renewal
         */
        if(!$result['status']){

            if(isset($request->type) && $request->type === 'apply_card'){
                return response()->json(['message' => "Error with your credit card"], 400);
            }

            if(isset($request->need_ccbill)){

                try {
                    $url = CcbillUtils::jpostMultiSubscriptions($leader, $subscriptions_with_errors_to_update, $active_subscriptions_ids_to_update);
                } catch (\Exception $exception) {
                    Logger::logException($exception);
                    return response()->json(['message' => "Error during the creation of the form"], 400);
                }

                return response()->json(['url' => $url], 303);
            }

            return response()->json(['message' => "Error with your credit card"], 400);
        }

        /*
         * From here the renewal process succeeded
         */
        $payment_method = LeaderCcbillData::query()->where('leader_id', $leader->id)
            ->where('active', true)
            ->latest()
            ->first();

        /*
         * Reactivate subscriptions without errors due renewal success
         */
        $builder->applyNewPaymentMethodToActiveSubscriptionsToUpdate($result['payment_method_id'] ?? $payment_method->id);

        /*
         * Reactivate subscriptions with errors due renewal successù
         * TODO: move this inside the builder
         */
        if($subscriptions_with_errors_to_update->count() > 0) {
            DB::beginTransaction();
            try {
                foreach ($subscriptions_with_errors_to_update as $subscription) {

                    $next_donation_at = SubscriptionUtils::computeNextDonationAt($subscription->subscription_at, now()->toDateTimeString());
                    $subscription->update([
                        'status' => 'active',
                        'last_subscription_at' => now(),
                        'leader_payment_method_id' => $result['payment_method_id'],
                        'next_donation_at' => $next_donation_at,
                        'valid_until_at' => $next_donation_at,
                        'deleted_at' => null
                    ]);

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
                }

                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();
                Logger::logException($exception);
                return response()->json(['message' => "Error during the process of the transactions", 'error' => $exception->getMessage()], 500);
            }
        }

        return response()->json();
    }
}
