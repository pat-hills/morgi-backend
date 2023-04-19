<?php


namespace App\Ccbill;


use App\Ccbill\TestUtils\FakeData;
use App\Logger\Logger;
use App\Mixpanel\Events\EventCreditCardError;
use App\Mixpanel\Events\EventRebillMorgiSuccess;
use App\Models\LeaderPayment;
use App\Models\Leader;
use App\Models\LeaderCcbillData;
use App\Models\MicromorgiPackage;
use App\Models\Subscription;
use App\Models\TransactionFailed;
use App\Models\User;
use App\Transactions\Morgi\TransactionGift;
use App\Utils\SubscriptionUtils;
use App\Utils\Utils;

class CcbillUtils
{
    public static function rebill(Subscription $subscription, $is_failed_transaction_flow = false)
    {
        $payment_method = LeaderCcbillData::query()->find($subscription->leader_payment_method_id);
        if(!$payment_method){
            throw new \Exception("Cannot retrieve leader's CCBill data");
        }

        $ccbill = new CcbillRebill(
            $payment_method->clientAccnum,
            $payment_method->clientSubacc,
            $subscription->amount,
            $payment_method->subscriptionId,
            $payment_method->subscriptionCurrencyCode,
            true
        );

        try {
            $result = $ccbill->fetchCcbillRebillApi();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        $leader = Leader::query()->find($subscription->leader_id);

        if((isset($result['approved']) && $result['approved'] == 1) || (FakeData::isFakeCCBillActive() && $payment_method->subscriptionId === 'CCBILL-INTERNAL-TEST')) {

            if($is_failed_transaction_flow){
                TransactionFailed::where('subscription_id', $subscription->id)->delete();
            }

            $ccbill_subscriptionId = (FakeData::isFakeCCBillActive() && $payment_method->subscriptionId === 'CCBILL-INTERNAL-TEST')
                ? 'CCBILL-INTERNAL-TEST'
                : $result['subscriptionId'];

            try {
                TransactionGift::create(
                    $subscription->rookie_id,
                    $subscription->leader_id,
                    $subscription->amount,
                    $subscription->id,
                    $payment_method->id,
                    $ccbill_subscriptionId,
                    false,
                    null,
                    true
                );
            }catch (\Exception $e){
                throw new \Exception("Error during the creation of the transactions");
            }

            $payment_method->update(['active' => true]);

            $next_donation_at = SubscriptionUtils::computeNextDonationAt($subscription->subscription_at, now()->toDateTimeString());
            $subscription->update([
                'status' => 'active',
                'last_subscription_at' => now(),
                'next_donation_at' => $next_donation_at,
                'valid_until_at' => $next_donation_at,
                'deleted_at' => null
            ]);

            try {
                EventRebillMorgiSuccess::config($subscription->leader_id, $subscription->rookie_id, $subscription->amount);
            } catch (\Exception $exception) {
                Logger::logException($exception);
            }

            return;
        }

        $error = CcbillApiErrorCodes::getError($result[0] ?? $result['approved']);
        $leader->createOrUpdateFailedTransaction($subscription, $error['description'], $result[0] ?? $result['approved']);
        $payment_method->update(['active' => false]);

        try {
            EventCreditCardError::config($leader->id, $subscription->amount, 'Card issue', $error['description']);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        if($subscription->status !== 'unsufficent_funds'){
            $subscription->update([
                'status' => 'unsufficent_funds',
                'failed_at' => now()
            ]);
        }
    }

    public static function jpostMultiSubscriptions(Leader $leader, $subscriptions_with_errors_to_update, $active_subscriptions_ids_to_update = null)
    {
        $leader_user = User::find($leader->id);
        $amount = $subscriptions_with_errors_to_update->sum('amount');
        $subscriptions_id = $subscriptions_with_errors_to_update->pluck('id')->toArray();
        $currency = CcbillCurrencyCodes::getCurrencyCode($leader_user->currency);

        try {
            return CcbillFormUtils::createRenewForm($leader->id, $amount, $currency, $subscriptions_id, $active_subscriptions_ids_to_update);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function jpostSubscription(Leader $leader, $rookie_id, $amount, $rookie_first_name)
    {
        $leader_user = User::find($leader->id);
        $currency = CcbillCurrencyCodes::getCurrencyCode($leader_user->currency);

        try {
            return CcbillFormUtils::createGiftForm($leader->id, $rookie_id, $amount, $currency, $rookie_first_name);
        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    public static function jpostMicromorgi(Leader $leader, $amount, $price)
    {
        $leader_user = User::find($leader->id);
        $currency = CcbillCurrencyCodes::getCurrencyCode($leader_user->currency);

        try {
            return CcbillFormUtils::createMicromorgiForm($leader->id, $amount, $price, $currency);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function oneTimeTransaction($payment_method, $amount, $is_subscription = false, $ip_address = null)
    {
        $ccbill = new CcbillRebill(
            $payment_method->clientAccnum,
            $payment_method->clientSubacc,
            $amount,
            $payment_method->subscriptionId,
            $payment_method->subscriptionCurrencyCode,
            $is_subscription
        );

        try {
            $payload = $ccbill->getPayload();
            $result = $ccbill->fetchCcbillRebillApi();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        if((isset($result['approved']) && $result['approved']==1) || (FakeData::isFakeCCBillActive() && $payment_method->subscriptionId === 'CCBILL-INTERNAL-TEST')){
            $subscriptionId = (FakeData::isFakeCCBillActive() && $payment_method->subscriptionId === 'CCBILL-INTERNAL-TEST')
                ? 'CCBILL-INTERNAL-TEST'
                : $result['subscriptionId'];
            return ['status' => true, 'subscriptionId' => $subscriptionId];
        }

        $currency_type = ($is_subscription) ? 'morgi' : 'micro_morgi';
        $mm_amount = ($is_subscription)
            ? $amount
            : MicromorgiPackage::getDollarAmount($amount);

        $type = ($is_subscription) ? 'rebill' : 'mm_purchase';

        $error = CcbillApiErrorCodes::getError($result[0] ?? $result['approved']);
        $payment_country = (isset($ip_address)) ? Utils::ipInfo($ip_address) : null;

        LeaderPayment::query()->create([
            'status' => 'failed',
            'leader_id' => $payment_method->leader_id,
            'currency_type' => $currency_type,
            'amount' => $mm_amount,
            'dollar_amount' => $amount,
            'ccbill_failureReason' => $error['description'],
            'type' => $type,
            'ccbill_failureCode' => $result[0] ?? $result['approved'],
            'leader_payment_method_id' => $payment_method->id,
            'ip_address' => $ip_address,
            'payment_country' => $payment_country,
            'payload' => $payload
        ]);

        return ['status' => false];
    }

    public static function refund($subscriptionId, $leader_payment_id = null)
    {
        if($subscriptionId === 'CCBILL-INTERNAL-TEST' && isset($leader_payment_id)){
            try {
                FakeData::fakeRefund($leader_payment_id);
            }catch (\Exception $exception){
                throw new \Exception($exception->getMessage());
            }
            return;
        }

        $ccbill = new CcbillSubscriptionManagement($subscriptionId);
        $refund_result = $ccbill->voidOrRefund();
        $is_approved = isset($refund_result[0]) && $refund_result[0]==1;

        if(!in_array(env('APP_ENV'), ['prod', 'production'])){
            return;
        }

        if(!$is_approved){
            throw new \Exception("[CCBill] Error to refund transaction in " . env('APP_ENV'));
        }
    }
}
