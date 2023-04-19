<?php


namespace App\Webhooks\Ccbill;


use App\Logger\Logger;
use App\Mixpanel\Events\EventBuyMicromorgiSuccess;
use App\Mixpanel\Events\EventCreditCardError;
use App\Models\Leader;
use App\Models\LeaderPayment;
use App\Models\PubnubChannel;
use App\Models\Rookie;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\TransactionHandshake;
use App\Models\User;
use App\Transactions\MicroMorgi\TransactionBoughtMicromorgi;
use App\Transactions\Morgi\TransactionGift;
use App\Utils\FreeConnectionChannelUtils;
use App\Utils\Subscription\Create\SubscriptionCreateUtils;
use App\Utils\SubscriptionUtils;
use App\Utils\TransactionRefundUtils;
use App\Utils\Utils;
use Illuminate\Support\Facades\DB;

class CCbillWebhookHandler
{
    public static function handleRefund($eventType, $request)
    {
        if(!isset($request->subscriptionId, $request->transactionId)){
            throw new \Exception("subscriptionId or transactionId not set");
        }

        $leader_payment = LeaderPayment::query();

        if(isset($request->subscriptionId)){
            $leader_payment = $leader_payment->where('ccbill_subscriptionId', $request->subscriptionId);
        }else{
            $leader_payment = $leader_payment->where('ccbill_transactionId', $request->transactionId);
        }

        $leader_payment = $leader_payment->first();

        /*
         * TESTING BEHAVIOR
         */
        if(isset($request->leader_payment_id)){
            $leader_payment = LeaderPayment::query()->find($request->leader_payment_id);
        }

        if(!isset($leader_payment)){
            throw new \Exception("Transaction not found");
        }

        $leader_payment->update([
            'status' => 'refunded',
            'refund_reason' => $request->reason,
            'refund_date' => $request->timestamp,
            'ccbill_transactionId' => $request->transactionId,
            'ccbill_subscriptionId' => $request->subscriptionId
        ]);

        if(strtolower($eventType)==='chargeback'){
            $leader = Leader::query()->where('id', $leader_payment->leader_id)->first();
            $leader->createLeaderStatusHistory('fraud', 'SYSTEM');
            $leader->update(['status' => 'fraud']);
        }

        $transaction = Transaction::where('leader_payment_id', $leader_payment->id)->first();

        if(isset($transaction)){

            DB::beginTransaction();
            try {
                TransactionRefundUtils::config($transaction)->refund(null, null, strtolower($eventType)==='chargeback');
                TransactionRefundUtils::createOrUpdateApprovedTransactionRefund($transaction);
                DB::commit();
            }catch (\Exception $exception){
                DB::rollBack();
                TransactionRefundUtils::createOrUpdateFailedTransactionRefund($transaction, $exception->getMessage());
                throw new \Exception($exception->getMessage());
            }
        }

    }

    public static function handleNewSaleFailure($type, $leader_id, $request)
    {
        DB::beginTransaction();
        try {

            $leader = Leader::query()->find($leader_id);

            $currency_map = [
                'micromorgi' => 'micro_morgi',
                'gift' => 'morgi',
                'renew' => 'morgi',
                'credit_card' => 'morgi'
            ];

            $metadata = $request->metadata;
            TransactionHandshake::query()->find($metadata->transaction_handshake_id)->update(['status' => 'failure']);

            $leader_payment_attributes = [
                'leader_id' => $leader_id, 'amount' => $metadata->amount, 'currency_type' => $currency_map[$type],
                'dollar_amount' => $metadata->dollar_amount, 'status' => 'failed',
                'ccbill_subscriptionId' => $request->subscriptionId, 'ccbill_transactionId' => $request->transactionId,
                'ccbill_failureReason'  => $request->failureReason, 'ccbill_failureCode' => $request->failureCode
            ];

            if($type==='micromorgi'){
                $leader_payment_attributes['type'] = 'mm_purchase';
            }elseif($type==='renew'){
                $leader_payment_attributes['type'] = 'rebill';
            }else{
                $leader_payment_attributes['type'] = 'first_purchase';
            }

            if(isset($request->ipAddress)){
                $payment_country = Utils::ipInfo($request->ipAddress);
                $leader_payment_attributes['payment_country'] = $payment_country;
            }

            LeaderPayment::query()->create($leader_payment_attributes);

            if($type==='renew'){

                $subscriptions = Subscription::query()
                    ->whereIn('id', $metadata->subscriptions_id)
                    ->where('leader_id', $leader_id)
                    ->get();

                foreach ($subscriptions as $subscription){
                    $subscription->update(['status' => 'failed']);
                    $leader->createOrUpdateFailedTransaction($subscription, $request->failureReason);
                }
            }

            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            throw new \Exception($exception->getMessage());
        }

        try {
            EventCreditCardError::config($leader_id, $metadata->dollar_amount, 'Card issue', $request->failureReason);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }
    }

    public static function handleNewSaleSuccess($type, $leader_id, $request)
    {
        DB::beginTransaction();
        try {

            $leader_payment_method = Leader::find($leader_id)->createPaymentMethod($request, true);
            $metadata = $request->metadata;
            TransactionHandshake::query()->find($metadata->transaction_handshake_id)->update(['status' => 'success']);

            if($type==='gift'){
                self::handleGiftSuccess($leader_id, $request, $leader_payment_method, $metadata);
            }

            if($type==='renew'){
                self::handleRenewSuccess($leader_id, $request, $leader_payment_method, $metadata);
            }

            if($type==='credit_card'){
                self::handleCrediCardSuccess($leader_id, $leader_payment_method, $metadata);
            }

            if($type==='micromorgi'){
                self::handleMicromorgiSuccess($leader_id, $request, $leader_payment_method, $metadata);
            }

            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            throw new \Exception($exception->getMessage());
        }
    }

    private static function handleGiftSuccess($leader_id, $request, $leader_payment_method, $metadata)
    {
        $subscription = Subscription::query()
            ->where('leader_id', $leader_id)
            ->where('rookie_id', $metadata->rookie_id)
            ->first();

        try {
            $leader = Leader::query()->find($leader_id);
            $paused_channel_exists = FreeConnectionChannelUtils::pausedChannelExists($leader->id, $metadata->rookie_id);

            if(!isset($subscription)){

                $subscription = SubscriptionCreateUtils::configure($leader_id, $metadata->rookie_id, $metadata->amount)
                    ->setLeaderPaymentMethodId($leader_payment_method->id)
                    ->create(false);

            }else{
                $subscription = $leader->reactivateGift($subscription, $metadata->amount, $leader_payment_method->id);
            }

            TransactionGift::create(
                $subscription->rookie_id,
                $subscription->leader_id,
                $subscription->amount,
                $subscription->id,
                $leader_payment_method->id,
                $request->subscriptionId,
                $paused_channel_exists,
                $request->ipAddress
            );

            if($paused_channel_exists){
                User::find($metadata->rookie_id)->increment('total_successful_paused_connections');
                User::find($leader_id)->increment('total_successful_paused_connections');
            }
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }

    private static function handleRenewSuccess($leader_id, $request, $leader_payment_method, $metadata)
    {
        try {
            Subscription::query()
                ->where('leader_id', $leader_id)
                ->whereIn('id', $metadata->update_subscriptions_id)
                ->update([
                    'leader_payment_method_id' => $leader_payment_method->id
                ]);

            $renew_subscriptions = Subscription::query()
                ->where('leader_id', $leader_id)
                ->whereIn('id', $metadata->renew_subscriptions_id)
                ->get();

            if($renew_subscriptions->count()>0){

                foreach ($renew_subscriptions as $subscription){

                    $next_donation_at = SubscriptionUtils::computeNextDonationAt($subscription->subscription_at, now()->toDateTimeString());

                    $subscription->update([
                        'status' => 'active', 'last_subscription_at' => now(), 'leader_payment_method_id' => $leader_payment_method->id,
                        'next_donation_at' => $next_donation_at, 'valid_until_at' => $next_donation_at, 'deleted_at' => null
                    ]);

                    TransactionGift::create(
                        $subscription->rookie_id,
                        $subscription->leader_id,
                        $subscription->amount,
                        $subscription->id,
                        $leader_payment_method->id,
                        $request->subscriptionId,
                        false,
                        $request->ipAddress
                    );

                    PubnubChannel::query()->where('subscription_id', $subscription->id)->update(['active' => true]);
                }
            }

        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

    }

    private static function handleMicromorgiSuccess($leader_id, $request, $leader_payment_method, $metadata)
    {
        try {
            TransactionBoughtMicromorgi::create(
                $leader_id,
                $metadata->dollar_amount,
                $leader_payment_method->id,
                $request->subscriptionId,
                $request->ipAddress
            );
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        try {
            EventBuyMicromorgiSuccess::config($leader_id, $metadata->amount, false);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }
    }

    private static function handleCrediCardSuccess($leader_id, $leader_payment_method, $metadata)
    {
        if(!isset($metadata->update_subscriptions_id)){
            return;
        }

        Subscription::query()
            ->where('leader_id', $leader_id)
            ->whereIn('id', $metadata->update_subscriptions_id)
            ->update(['leader_payment_method_id' => $leader_payment_method->id]);
    }
}
