<?php

namespace App\Transactions\MicroMorgi;

use App\LeaderPackages\AddMicromorgi;
use App\Models\ActivityLog;
use App\Models\MicromorgiPackage;
use App\Models\Transaction;
use App\Transactions\TransactionBuilder;
use App\Utils\ActivityLogsUtils;
use App\Utils\ReasonUtils;

class TransactionLeaderMicromorgiBonus extends TransactionBuilder
{
    public $type = 'bonus';

    public static function create(int $leader_id,
                                  int $micromorgi,
                                  int $admin_id = null,
                                  string $notes = null,
                                  string $admin_description = null): Transaction
    {
        $dollars = MicromorgiPackage::getDollarAmount($micromorgi);

        try {
            $builder = new TransactionLeaderMicromorgiBonus();
            $builder->setLeaderId($leader_id)
                ->setMicromorgi($micromorgi)
                ->setDollars($dollars);

            if(isset($admin_id)){
                $builder->setAdminId($admin_id);
            }

            if(isset($notes)){
                $builder->setNotes(ReasonUtils::ALL_REASON[$notes] ?? $notes);
            }

            if(isset($admin_description)){
                $builder->setAdminDescription($admin_description);
            }

            $builder->store()
                ->adaptBalances()
                ->storeActivityLog();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        if(!isset($builder->transaction)){
            throw new \Exception("Transaction was not created");
        }

        return $builder->transaction;
    }

    private function adaptBalances(): TransactionLeaderMicromorgiBonus
    {
        AddMicromorgi::config($this->leader_id, $this->micromorgi)
            ->setTransactionId($this->transaction->id)
            ->add();

        return $this;
    }

    private function storeActivityLog(): TransactionLeaderMicromorgiBonus
    {
        $this->activity_log = ActivityLog::create([
            'initiated_by' => 'system bonus',
            'internal_id' => ActivityLogsUtils::generateInternalId($this->leader_id),
            'leader_id' => $this->leader_id,
            'micromorgi' => $this->micromorgi,
            'dollars' => $this->dollars,
            'transaction_internal_id' => $this->transaction->internal_id,
            'admin_id' => $this->admin_id
        ]);

        return $this;
    }
}
