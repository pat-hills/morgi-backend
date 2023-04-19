<?php

namespace App\Transactions\MicroMorgi;

use App\Models\ActivityLog;
use App\Models\MicromorgiPackage;
use App\Models\Transaction;
use App\Transactions\TransactionBuilder;
use App\Utils\ActivityLogsUtils;
use App\Utils\ReasonUtils;

class TransactionRookieMicromorgiBonus extends TransactionBuilder
{
    public $type = 'bonus';

    public static function create(int $rookie_id,
                                  int $micromorgi,
                                  int $admin_id = null,
                                  string $notes = null,
                                  string $admin_description = null): Transaction
    {
        $dollars = MicromorgiPackage::getDollarAmount($micromorgi);

        try {
            $builder = new TransactionRookieMicromorgiBonus();
            $builder->setRookieId($rookie_id)
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

    private function adaptBalances(): TransactionRookieMicromorgiBonus
    {
        $this->rookie->pushMicromorgi($this->micromorgi, $this->taxed_micromorgi);
        $this->rookie->pushDollars($this->dollars, $this->taxed_dollars);

        return $this;
    }

    private function storeActivityLog(): TransactionRookieMicromorgiBonus
    {
        $this->activity_log = ActivityLog::create([
            'initiated_by' => 'system bonus',
            'internal_id' => ActivityLogsUtils::generateInternalId($this->rookie_id),
            'rookie_id' => $this->rookie_id,
            'micromorgi' => $this->micromorgi,
            'dollars' => $this->dollars,
            'transaction_internal_id' => $this->transaction->internal_id,
            'admin_id' => $this->admin_id
        ]);

        return $this;
    }
}
