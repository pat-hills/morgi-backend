<?php

namespace App\Transactions\MicroMorgi;

use App\Models\MicromorgiPackage;
use App\Models\Transaction;
use App\Transactions\TransactionBuilder;

class TransactionRookieFineMicromorgi extends TransactionBuilder
{
    public $type = 'fine';

    public static function create(int $rookie_id,
                                  int $micromorgi,
                                  int $admin_id = null,
                                  string $notes = null): Transaction
    {
        $dollars = MicromorgiPackage::getDollarAmount($micromorgi);

        try {
            $builder = new TransactionRookieFineMicromorgi();
            $builder->setRookieId($rookie_id)
                ->setMicromorgi($micromorgi)
                ->setDollars($dollars);

            if(isset($admin_id)){
                $builder->setAdminId($admin_id);
            }

            if(isset($notes)){
                $builder->setNotes($notes);
            }

            $builder->store()->adaptBalances();

        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        if(!isset($builder->transaction)){
            throw new \Exception("Transaction was not created");
        }

        return $builder->transaction;
    }

    private function adaptBalances(): TransactionRookieFineMicromorgi
    {
        $this->rookie->popMicromorgi($this->micromorgi, $this->taxed_micromorgi);
        $this->rookie->popDollars($this->dollars, $this->taxed_dollars);

        return $this;
    }
}
