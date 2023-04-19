<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRookie extends Model
{
    use HasFactory;

    protected $table = 'payments_rookies';

    protected $fillable = [
        'payment_id',
        'rookie_id',
        'reference',
        'status',
        'amount',
        'admin_id',
        'note',
        'approved_at',
        'rejected_at'
    ];
}
