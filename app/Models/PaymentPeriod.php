<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentPeriod extends Model
{
    use HasFactory;

    protected $table = 'payments_periods';

    protected $fillable = [
        'name',
        'start_date',
        'end_date'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public static function getLastDate()
    {
        $payment_period = self::query()->orderBy('end_date', 'DESC')->first();
        if(isset($payment_period)){
            return $payment_period->end_date;
        }

        return null;
    }

    public static function getLastDateTimestamp()
    {
        $payment_period = self::query()->orderBy('end_date', 'DESC')->first();
        if(isset($payment_period)){
            return $payment_period->created_at;
        }

        return null;
    }
}
