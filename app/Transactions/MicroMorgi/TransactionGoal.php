<?php

namespace App\Transactions\MicroMorgi;

use App\Logger\Logger;
use App\Models\ActivityLog;
use App\Models\GoalDonation;
use App\Models\MicromorgiPackage;
use App\Models\PubnubChannel;
use App\Models\Transaction;
use App\Telegram\TelegramUtils;
use App\Transactions\TransactionBuilder;
use App\Utils\ActivityLogsUtils;
use App\Utils\NotificationUtils;

class TransactionGoal extends TransactionBuilder
{
    public $type = 'goal';

    public static function create(int $rookie_id, int $leader_id, int $micromorgi, int $goal_id): Transaction
    {
        $dollars = MicromorgiPackage::getDollarAmount($micromorgi);

        try {
            $builder = new TransactionGoal();
            $builder->setRookieId($rookie_id)
                ->setLeaderId($leader_id)
                ->setMicromorgi($micromorgi)
                ->setDollars($dollars)
                ->hasPulse(true)
                ->setGoalId($goal_id)
                ->adaptBalances()
                ->store();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        if(!isset($builder->transaction)){
            throw new \Exception("Transaction was not created");
        }

        GoalDonation::create([
            'leader_id' => $leader_id,
            'goal_id' => $goal_id,
            'amount' => $micromorgi,
            'transaction_id' => $builder->transaction->id
        ]);

        try {
            $builder->sendNotifications();
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        return $builder->transaction;
    }

    private function adaptBalances(): TransactionGoal
    {
        $this->leader->popMicromorgi($this->micromorgi);
        return $this;
    }

    private function sendNotifications(): TransactionGoal
    {
        $attributes = [
            'amount_micromorgi' => $this->micromorgi,
            'ref_user_id' => $this->leader_id,
            'goal_id' => $this->transaction->goal_id
        ];

        NotificationUtils::sendNotification(
            $this->rookie_id,
            "transaction_goal",
            now(),
            $attributes
        );

        return $this;
    }
}

