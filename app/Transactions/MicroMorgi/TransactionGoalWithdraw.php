<?php

namespace App\Transactions\MicroMorgi;

use App\Models\Goal;
use App\Models\GoalDonation;
use App\Models\MicromorgiPackage;
use App\Models\Transaction;
use App\Transactions\TransactionBuilder;

class TransactionGoalWithdraw extends TransactionBuilder
{
    public $type = 'goal_withdraw';

    public static function create(int $goal_id): Transaction
    {
        $goal = Goal::find($goal_id);
        $goal_donations = GoalDonation::query()->where('goal_id', $goal_id)
            ->where('status', 'successful')
            ->get();

        if($goal_donations->isEmpty()){
            throw new \Exception("This goal does not have donations");
        }

        $micromorgi = $goal_donations->sum('amount');
        $dollars = MicromorgiPackage::getDollarAmount($micromorgi);

        try {
            $builder = new TransactionGoalWithdraw();
            $builder->setRookieId($goal->rookie_id)
                ->setMicromorgi($micromorgi)
                ->setDollars($dollars)
                ->hasPulse(false)
                ->setGoalId($goal_id)
                ->adaptBalances()
                ->store();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        if(!isset($builder->transaction)){
            throw new \Exception("Transaction was not created");
        }

        GoalDonation::query()->where('goal_id', $goal->id)->update([
            'is_withdrawn' => true
        ]);

        return $builder->transaction;
    }

    private function adaptBalances(): TransactionGoalWithdraw
    {
        $this->rookie->pushMicromorgi($this->micromorgi, $this->taxed_micromorgi);
        $this->rookie->pushDollars($this->dollars, $this->taxed_dollars);
        return $this;
    }
}
