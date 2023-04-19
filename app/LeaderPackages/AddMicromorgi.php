<?php

namespace App\LeaderPackages;

use App\Models\Leader;
use App\Models\LeaderPackage;

class AddMicromorgi
{
    public $amount;
    public $leader;
    public $transaction_id = null;
    public $leader_payment_id = null;

    public static function config(int $leader_id, int $amount): AddMicromorgi
    {
        return new AddMicromorgi($leader_id, $amount);
    }

    public function __construct(int $leader_id, int $amount)
    {
        $this->amount = $amount;
        $this->leader = Leader::find($leader_id);
    }

    public function setTransactionId(int $transaction_id): AddMicromorgi
    {
        $this->transaction_id = $transaction_id;
        return $this;
    }

    public function setLeaderPaymentId(int $leader_payment_id): AddMicromorgi
    {
        $this->leader_payment_id = $leader_payment_id;
        return $this;
    }

    public function add(): LeaderPackage
    {
        $this->leader->update([
            'micro_morgi_balance' => $this->leader->micro_morgi_balance + $this->amount
        ]);

        return LeaderPackage::create([
            'amount' => $this->amount,
            'leader_id' => $this->leader->id,
            'amount_available' => $this->amount,
            'leader_payment_id' => $this->leader_payment_id,
            'transaction_id' => $this->transaction_id
        ]);
    }
}
