<?php

namespace App\Transactions\MicroMorgi;

use App\Logger\Logger;
use App\Models\ActivityLog;
use App\Models\MicromorgiPackage;
use App\Models\PubnubChannel;
use App\Models\Transaction;
use App\Telegram\TelegramUtils;
use App\Transactions\TransactionBuilder;
use App\Utils\ActivityLogsUtils;
use App\Utils\NotificationUtils;

class TransactionChat extends TransactionBuilder
{
    public $type = 'chat';

    public static function create(int $rookie_id, int $leader_id, int $micromorgi): Transaction
    {
        $dollars = MicromorgiPackage::getDollarAmount($micromorgi);

        try {
            $builder = new TransactionChat();
            $builder->setRookieId($rookie_id)
                ->setLeaderId($leader_id)
                ->setMicromorgi($micromorgi)
                ->setDollars($dollars)
                ->hasPulse(true)
                ->store()
                ->adaptBalances()
                ->storeActivityLog();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        try {
            $builder->sendNotifications()
                ->sendTelegramMessages();
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        if(!isset($builder->transaction)){
            throw new \Exception("Transaction was not created");
        }

        return $builder->transaction;
    }

    private function adaptBalances(): TransactionChat
    {
        $this->leader->popMicromorgi($this->micromorgi);
        $this->rookie->pushMicromorgi($this->micromorgi, $this->taxed_micromorgi);
        $this->rookie->pushDollars($this->dollars, $this->taxed_dollars);
        return $this;
    }

    private function storeActivityLog(): TransactionChat
    {
        $this->activity_log = ActivityLog::create([
            'internal_id' => ActivityLogsUtils::generateInternalId($this->leader_id),
            'rookie_id' => $this->rookie_id,
            'leader_id' => $this->leader_id,
            'micromorgi' => $this->micromorgi,
            'dollars' => $this->dollars,
            'transaction_internal_id' => $this->transaction->internal_id
        ]);

        return $this;
    }

    private function sendNotifications(): TransactionChat
    {
        $attributes = [
            'amount_micromorgi' => $this->micromorgi,
            'ref_user_id' => $this->leader_id
        ];

        NotificationUtils::sendNotification(
            $this->rookie_id,
            "rookie_receive_micromorgi",
            now(),
            $attributes
        );

        return $this;
    }

    private function sendTelegramMessages(): TransactionChat
    {
        if(isset($this->rookie_user->joined_telegram_bot_at)){

            $channel = PubnubChannel::search($this->leader_id, $this->rookie_id)->first();
            if(isset($channel)){
                $attributes = [
                    'leader_username' => $this->leader_user->username,
                    'amount' => $this->micromorgi,
                    'channel_name' => $channel->name
                ];

                TelegramUtils::sendTelegramNotifications(
                    $this->rookie_user->telegram_chat_id,
                    'micromorgi_received',
                    $attributes,
                    $this->rookie_id
                );
            }
        }

        return $this;
    }
}
