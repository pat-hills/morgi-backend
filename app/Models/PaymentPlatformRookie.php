<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentPlatformRookie extends Model
{
    use HasFactory;

    protected $table = 'payments_platforms_rookies';

    protected $fillable = [
        'rookie_id',
        'payment_platform_id',
        'payment_info',
        'main'
    ];

    protected $hidden = [
        'rookie_id',
        'created_at',
        'updated_at',
        'payment_platform_id'
    ];

    protected $appends = [
        'payment_platform'
    ];

    protected $casts = [
        'main' => 'boolean'
    ];

    public function getPaymentPlatformAttribute()
    {
        return PaymentPlatform::find($this->payment_platform_id);
    }
}
