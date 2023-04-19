<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVideoFieldToRookiesPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rookies_points', function (Blueprint $table) {
            $table->integer('video')->default(0)->after('photo');
        });

        Schema::table('rookies_points_histories', function (Blueprint $table) {
            $table->integer('video')->default(0)->after('photo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rookies_points', function (Blueprint $table) {
            $table->dropColumn('video')->default(0);
        });

        Schema::table('rookies_points_histories', function (Blueprint $table) {
            $table->dropColumn('video')->default(0);
        });
    }
}
