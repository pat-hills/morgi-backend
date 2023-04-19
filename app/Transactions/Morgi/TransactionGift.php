<?php

namespace App\Transactions\Morgi;

use App\Enums\PubnubBroadcastEnum;
use App\Logger\Logger;
use App\Models\ActivityLog;
use App\Models\LeaderPayment;
use App\Models\PubnubBroadcast;
use App\Models\PubnubChannel;
use App\Models\Transaction;
use App\Telegram\TelegramUtils;
use App\Transactions\TransactionBuilder;
use App\Utils\ActivityLogsUtils;
use App\Utils\NotificationUtils;
use App\Utils\Pubnub\PubnubBroadcastUtils;
use App\Utils\Utils;

class TransactionGift extends TransactionBuilder
{
    public $type = 'gift';
    private $is_first_subscription_transaction;
    private $channel;
    private $is_rebill;

    public static function create(int $rookie_id,
                                  int $leader_id,
                                  int $morgi,
                                  int $subscription_id,
                                  int $payment_method_id,
                                  string $ccbill_subscriptionId,
                                  bool $paused_channel_exists = false,
                                  string $ip_address = null,
                                  bool $is_rebill = false): Transaction
    {
        try {
            $builder = new TransactionGift();
            $builder->channel = PubnubChannel::where('rookie_id', $rookie_id)->where('leader_id', $leader_id)->first();
            $builder->is_rebill = $is_rebill;
            $builder->setRookieId($rookie_id)
                ->setLeaderId($leader_id)
                ->setSubscriptionId($subscription_id)
                ->setMorgi($morgi)
                ->setDollars($morgi)
                ->createLeaderPayment($payment_method_id, $ccbill_subscriptionId, $ip_address)
                ->hasPulse(true)
                ->store()
                ->adaptBalances()
                ->storeActivityLog();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        try {
            $builder->sendNotifications()
                ->sendTelegramMessages()
                ->sendPubnubBroadcast($paused_channel_exists);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        if(!isset($builder->transaction)){
            throw new \Exception("Transaction was not created");
        }

        return $builder->transaction;
    }

    private function createLeaderPayment(int $payment_method_id, string $ccbill_subscriptionId, string $ip_address = null): TransactionGift
    {
        $this->is_first_subscription_transaction = !LeaderPayment::query()
            ->where('subscription_id', $this->subscription_id)
            ->where('status', 'paid')
            ->exists();
        $leader_payment_type = ($this->is_first_subscription_transaction) ? 'first_purchase' : 'rebill';
        $payment_country = (isset($ip_address)) ? Utils::ipInfo($ip_address) : null;

        $leader_payment = LeaderPayment::create([
            'leader_id' => $this->leader_id,
            'currency_type' => 'morgi',
            'status' => 'paid',
            'amount' => $this->morgi,
            'dollar_amount' => $this->dollars,
            'ip_address' => $ip_address,
            'leader_payment_method_id' => $payment_method_id,
            'ccbill_subscriptionId' => $ccbill_subscriptionId,
            'subscription_id' => $this->subscription_id,
            'payment_country' => $payment_country,
            'type' => $leader_payment_type
        ]);

        $this->leader_payment_id = $leader_payment->id;

        return $this;
    }

    private function adaptBalances(): TransactionGift
    {
        $this->rookie->pushMorgi($this->morgi, $this->taxed_morgi);
        $this->rookie->pushDollars($this->dollars, $this->taxed_dollars);

        return $this;
    }

    private function storeActivityLog(): TransactionGift
    {
        $this->activity_log = ActivityLog::create([
            'internal_id' => ActivityLogsUtils::generateInternalId($this->transaction->leader_id),
            'rookie_id' => $this->transaction->rookie_id,
            'leader_id' => $this->transaction->leader_id,
            'morgi' => $this->transaction->morgi,
            'transaction_internal_id' => $this->transaction->internal_id,
            'dollars' => $this->dollars
        ]);

        return $this;
    }

    private function sendPubnubBroadcast(bool $paused_channel_exists): TransactionGift
    {
        if(!isset($this->channel)){
            throw new \Exception("Unable to retrieve channel");
        }

        $type = ($paused_channel_exists)
            ? PubnubBroadcastEnum::TYPE_GIFT_AFTER_PAUSE_TRANSACTION
            : PubnubBroadcastEnum::TYPE_GIFT_TRANSACTION;

        PubnubBroadcastUtils::config($this->channel, $type)
            ->setLeaderId($this->leader_id)
            ->setSenderId($this->leader_id)
            ->setRookieId($this->rookie_id)
            ->setTransactionId($this->transaction->id)
            ->send();

        return $this;
    }

    private function sendNotifications(): TransactionGift
    {
        if($this->is_rebill){
            NotificationUtils::sendNotification(
                $this->leader_id,
                "leader_renewed_gift",
                now(),
                [
                    'ref_user_id' => $this->rookie_id,
                    'amount_morgi' => $this->morgi
                ]
            );

            NotificationUtils::sendNotification(
                $this->rookie_id,
                "rookie_renewed_gift",
                now(),
                [
                    'ref_user_id' => $this->leader_id,
                    'amount_morgi' => $this->morgi
                ]
            );
        }

        return $this;
    }

    private function sendTelegramMessages(): TransactionGift
    {
        if(isset($this->rookie_user->joined_telegram_bot_at)){

            $subscription_transactions_count = Transaction::query()
                ->where('subscription_id', $this->subscription_id)
                ->count();

            if($subscription_transactions_count > 1){

                if(!isset($this->channel)){
                    throw new \Exception("Unable to retrieve channel");
                }

                TelegramUtils::sendTelegramNotifications(
                    $this->rookie_user->telegram_chat_id,
                    'recurring_gift',
                    [
                        'leader_username' => $this->leader_user->username,
                        'amount' => $this->morgi,
                        'channel_name' => $this->channel->name
                    ],
                    $this->rookie_user->id
                );
            }
        }

        return $this;
    }
}
