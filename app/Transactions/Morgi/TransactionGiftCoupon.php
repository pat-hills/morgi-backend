<?php

namespace App\Transactions\Morgi;

use App\Logger\Logger;
use App\Models\ActivityLog;
use App\Models\PubnubChannel;
use App\Models\Transaction;
use App\Telegram\TelegramUtils;
use App\Transactions\TransactionBuilder;
use App\Utils\ActivityLogsUtils;

class TransactionGiftCoupon extends TransactionBuilder
{
    public $type = 'gift';
    private $channel;

    public static function create(int $rookie_id,
                                  int $leader_id,
                                  int $morgi,
                                  int $subscription_id,
                                  int $coupon_id): Transaction
    {
        try {
            $builder = new TransactionGiftCoupon('not_refund_gift_with_coupon');
            $builder->channel = PubnubChannel::where('rookie_id', $rookie_id)->where('leader_id', $leader_id)->first();
            $builder->setRookieId($rookie_id)
                ->setLeaderId($leader_id)
                ->setSubscriptionId($subscription_id)
                ->setCouponId($coupon_id)
                ->setMorgi($morgi)
                ->setDollars($morgi)
                ->hasPulse(true)
                ->store()
                ->adaptBalances()
                ->storeActivityLog();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        try {
            $builder->sendTelegramMessages();
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        if(!isset($builder->transaction)){
            throw new \Exception("Transaction was not created");
        }

        return $builder->transaction;
    }

    private function adaptBalances(): TransactionGiftCoupon
    {
        $this->rookie->pushMorgi($this->morgi, $this->taxed_morgi);
        $this->rookie->pushDollars($this->dollars, $this->taxed_dollars);

        return $this;
    }

    private function storeActivityLog(): TransactionGiftCoupon
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

    private function sendTelegramMessages(): TransactionGiftCoupon
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
