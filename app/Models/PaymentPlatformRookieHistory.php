<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentPlatformRookieHistory extends Model
{
    use HasFactory;

    protected $table = 'payments_platforms_rookies_histories';

    protected $fillable = [
        'payments_platforms_rookies_id',
        'rookie_id',
        'payment_platform_id',
        'is_reset'
    ];
}
