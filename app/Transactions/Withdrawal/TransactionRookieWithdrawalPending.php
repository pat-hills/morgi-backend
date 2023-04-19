<?php

namespace App\Transactions\Withdrawal;

use App\Models\ActivityLog;
use App\Models\Transaction;
use App\Transactions\TransactionBuilder;
use App\Utils\ActivityLogsUtils;

class TransactionRookieWithdrawalPending extends TransactionBuilder
{
    public $type = 'withdrawal_pending';

    public static function create(int $rookie_id,
                                  int $payment_rookie_id): Transaction
    {
        try {
            $builder = new TransactionRookieWithdrawalPending();
            $builder->setRookieId($rookie_id)
                ->setPaymentRookieId($payment_rookie_id)
                ->setMorgi($builder->rookie->untaxed_morgi_balance)
                ->setMicromorgi($builder->rookie->untaxed_micro_morgi_balance)
                ->setDollars($builder->rookie->untaxed_withdrawal_balance)
                ->overrideTaxedMorgi($builder->rookie->morgi_balance)
                ->overrideTaxedMicromorgi($builder->rookie->micro_morgi_balance)
                ->overrideTaxedDollars($builder->rookie->withdrawal_balance)
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

    private function adaptBalances(): TransactionRookieWithdrawalPending
    {
        $this->rookie->popMorgi($this->morgi, $this->taxed_morgi);
        $this->rookie->popMicromorgi($this->micromorgi, $this->taxed_micromorgi);
        $this->rookie->popDollars($this->dollars, $this->taxed_dollars);

        return $this;
    }

    private function storeActivityLog(): TransactionRookieWithdrawalPending
    {
        $this->activity_log = ActivityLog::create([
            'refund_type' => 'withdrawal_pending',
            'initiated_by' => 'morgi',
            'internal_id' => ActivityLogsUtils::generateInternalId($this->rookie_id),
            'transaction_internal_id' => $this->internal_id,
            'rookie_id' => $this->rookie_id,
            'morgi' => $this->taxed_morgi,
            'micromorgi' => $this->taxed_micromorgi,
            'dollars' => $this->taxed_dollars
        ]);

        return $this;
    }
}
