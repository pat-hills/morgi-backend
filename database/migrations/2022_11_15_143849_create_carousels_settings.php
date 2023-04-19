<?php

use App\Enums\CarouselTypeEnum;
use App\Models\CarouselSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class CreateCarouselsSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carousels_settings', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->boolean('is_active');
            $table->timestamps();
        });

        CarouselSetting::query()->insert([
            ['type' => CarouselTypeEnum::HORIZONTAL, 'is_active' => true, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['type' => CarouselTypeEnum::VERTICAL, 'is_active' => false, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carousels_settings');
    }
}
