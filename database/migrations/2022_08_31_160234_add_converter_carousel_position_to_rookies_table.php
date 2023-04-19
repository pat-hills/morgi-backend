<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConverterCarouselPositionToRookiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rookies', function (Blueprint $table) {
            $table->unsignedBigInteger('converter_carousel_position_id')->nullable()->after('is_converter');
        });

        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('converters_carousel_order');
            $table->timestamps();
        });

        \App\Models\SystemSetting::query()->create([
            'converters_carousel_order' => 'randomly'
        ]);

        Schema::create('converters_carousel_positions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('position');
            $table->timestamps();
        });

        $converters_carousel_positions = [1, 4, 7, 11];
        foreach ($converters_carousel_positions as $position){
            \App\Models\ConverterCarouselPosition::query()->create([
                'position' => $position
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rookies', function (Blueprint $table) {
            $table->dropColumn('converter_carousel_position_id');
        });

        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('converters_carousel_positions');
    }
}
