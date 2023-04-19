<?php

namespace App\Transactions\Morgi;

use App\Models\Transaction;
use App\Transactions\TransactionBuilder;

class TransactionRookieFineMorgi extends TransactionBuilder
{
    public $type = 'fine';

    public static function create(int $rookie_id,
                                  int $morgi,
                                  int $admin_id = null,
                                  string $notes = null): Transaction
    {
        try {
            $builder = new TransactionRookieFineMorgi();
            $builder->setRookieId($rookie_id)
                ->setMorgi($morgi)
                ->setDollars($morgi);

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

    private function adaptBalances(): TransactionRookieFineMorgi
    {
        $this->rookie->popMorgi($this->morgi, $this->taxed_morgi);
        $this->rookie->popDollars($this->dollars, $this->taxed_dollars);

        return $this;
    }
}
