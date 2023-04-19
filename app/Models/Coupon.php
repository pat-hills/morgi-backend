<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'to_transaction_id',
        'leader_id',
        'currency_value',
        'is_spent',
        'from_transaction_id'
    ];

    public function getToTransactionAttribute()
    {
        if (!empty($this->to_transaction_id)){
            return Transaction::query()->find($this->to_transaction_id);
        }

        return null;
    }

    public function getFromTransactionAttribute()
    {
        if (!empty($this->from_transaction_id)){
            return Transaction::query()->find($this->from_transaction_id);
        }

        return null;
    }

    public function spend(int $to_transaction_id): void
    {
        $this->update([
            'is_spent' => true,
            'to_transaction_id' => $to_transaction_id
        ]);
    }
}
