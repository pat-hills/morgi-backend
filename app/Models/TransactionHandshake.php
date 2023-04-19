<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionHandshake extends Model
{
    use HasFactory;

    protected $table = 'transactions_handshake';

    protected $fillable = [
        'type',
        'status',
        'amount',
        'dollar_amount',
        'rookie_id',
        'jpost_url',
        'user_id',
        'subscription_id',
        'leader_payment_id'
    ];

    public function getHasActivePaymentMethodAttribute()
    {
        return LeaderCcbillData::query()->where('leader_id', $this->user_id)
            ->where('active', true)
            ->exists();
    }
}
