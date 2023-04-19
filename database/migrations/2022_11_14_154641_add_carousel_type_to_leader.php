<?php

use App\Enums\CarouselTypeEnum;
use App\Models\Leader;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCarouselTypeToLeader extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leaders', function (Blueprint $table) {
            $table->string('carousel_type')->after('global_id');
        });

        Leader::query()->update(['carousel_type' => CarouselTypeEnum::HORIZONTAL]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leaders', function (Blueprint $table) {
            $table->dropColumn('carousel_type');
        });
    }
}
