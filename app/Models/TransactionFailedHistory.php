<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionFailedHistory extends Model
{
    use HasFactory;

    protected $table = 'transactions_failed_history';

    protected $fillable = [
        'subscription_id',
        'leader_payment_method_id',
        'reason',
        'amount'
    ];
}
