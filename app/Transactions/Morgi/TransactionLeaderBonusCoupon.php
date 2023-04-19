<?php

namespace App\Transactions\Morgi;

use App\Models\Transaction;
use App\Transactions\TransactionBuilder;
use App\Utils\ReasonUtils;

class TransactionLeaderBonusCoupon extends TransactionBuilder
{
    public $type = 'bonus';

    public static function create(int $leader_id,
                                  int $morgi,
                                  int $admin_id,
                                  string $notes,
                                  string $admin_description = null): Transaction
    {
        try {
            $builder = new TransactionLeaderBonusCoupon();
            $builder->setLeaderId($leader_id)
                ->setAdminId($admin_id)
                ->setMorgi($morgi)
                ->setDollars($morgi)
                ->setNotes(ReasonUtils::ALL_REASON[$notes] ?? $notes)
                ->setAdminDescription($admin_description)
                ->store();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        if(!isset($builder->transaction)){
            throw new \Exception("Transaction was not created");
        }

        return $builder->transaction;
    }
}
