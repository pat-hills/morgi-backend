<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaderPackageTransaction extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = "leaders_packages_transactions";

    protected $fillable = [
        'amount',
        'leader_package_id',
        'transaction_id',
        'is_refunded'
    ];

    protected $casts = [
        'is_refunded' => 'boolean'
    ];
}
