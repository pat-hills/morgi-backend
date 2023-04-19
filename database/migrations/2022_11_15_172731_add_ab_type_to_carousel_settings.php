<?php

use App\Enums\CarouselTypeEnum;
use App\Models\CarouselSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;

class AddAbTypeToCarouselSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        CarouselSetting::query()->create([
            'type' => CarouselTypeEnum::AB,
            'is_active' => false
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        CarouselSetting::query()->where('type', CarouselTypeEnum::AB)->delete();
    }
}
