<?php

namespace App\Transactions\Morgi;

use App\Models\ActivityLog;
use App\Models\Transaction;
use App\Transactions\TransactionBuilder;
use App\Utils\ActivityLogsUtils;
use App\Utils\ReasonUtils;

class TransactionRookieMorgiBonus extends TransactionBuilder
{
    public $type = 'bonus';

    public static function create(int $rookie_id,
                                  int $morgi,
                                  string $notes): Transaction
    {
        try {
            $builder = new TransactionRookieMorgiBonus();
            $builder->setRookieId($rookie_id)
                ->setMorgi($morgi)
                ->setDollars($morgi)
                ->setNotes(ReasonUtils::ALL_REASON[$notes] ?? $notes)
                ->setAdminDescription($notes)
                ->store()
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

    private function adaptBalances(): TransactionRookieMorgiBonus
    {
        $this->rookie->pushMorgi($this->morgi, $this->taxed_morgi);
        $this->rookie->pushDollars($this->dollars, $this->taxed_dollars);

        return $this;
    }

    private function storeActivityLog(): TransactionRookieMorgiBonus
    {
        $this->activity_log = ActivityLog::create([
            'internal_id' => ActivityLogsUtils::generateInternalId($this->rookie_id),
            'initiated_by' => 'system raffle',
            'rookie_id' => $this->rookie_id,
            'morgi' => $this->morgi,
            'dollars' => $this->dollars,
            'transaction_internal_id' => $this->transaction->internal_id
        ]);

        return $this;
    }
}
