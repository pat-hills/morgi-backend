<?php

namespace App\Models;

use App\Enums\MicromorgiPackagesEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MicromorgiPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'micromorgi_count',
        'price',
        'sort_order'
    ];

    public static function getDollarAmount(int $amount)
    {
        return $amount * MicromorgiPackagesEnum::ONE_MICROMORGI_DOLLAR_PRICE;
    }

    public static function getMicromorgiAmount(float $dollar_amount)
    {
        return $dollar_amount / MicromorgiPackagesEnum::ONE_MICROMORGI_DOLLAR_PRICE;
    }
}
