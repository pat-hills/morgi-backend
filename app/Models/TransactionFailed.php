<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionFailed extends Model
{
    use HasFactory;

    protected $table = 'transactions_failed';

    public $timestamps = false;

    protected $fillable = [
        'subscription_id',
        'attempts',
        'last_attempt_at'
    ];
}
