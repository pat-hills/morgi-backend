<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPackage extends Model
{
    use HasFactory;

    protected $table = 'subscription_packages';

    public $timestamps = false;

    protected $fillable = [
        'amount',
        'dollar_amount',
        'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean'
    ];
}
