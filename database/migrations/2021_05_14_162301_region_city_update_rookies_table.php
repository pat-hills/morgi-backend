<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RegionCityUpdateRookiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('rookies', function (Blueprint $table) {
            $table->dropColumn('city');
            $table->dropColumn('region');
            $table->bigInteger('city_id')->nullable(true)->after('country_id');
            $table->bigInteger('region_id')->nullable(true)->after('country_id');
            $table->string('region_name')->nullable(true)->after('country_id');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rookies', function (Blueprint $table) {
            $table->string('city')->nullable(true)->after('country_id');
            $table->string('region')->nullable(true)->after('country_id');
            $table->dropColumn('city_id');
            $table->dropColumn('region_id');
            $table->dropColumn('region_name');
        });
    }
}
