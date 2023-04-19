<?php

namespace App\Models;

use App\Enums\CarouselTypeEnum;
use App\Logger\Logger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class CarouselSetting extends Model
{
    use HasFactory;

    protected $table = 'carousels_settings';

    protected $fillable = [
        'type',
        'is_active'
    ];

    public static function getActiveType()
    {
        $active_type = CarouselSetting::query()->where('is_active', true)->first();

        if (!isset($active_type)) {
            Logger::logMessage('Unable to retrieve carousel active type');
            return CarouselTypeEnum::HORIZONTAL;
        }

        if ($active_type->type === CarouselTypeEnum::AB) {
            return Arr::random(CarouselTypeEnum::TYPES_FILLABLE);
        }

        return $active_type->type;
    }
}
