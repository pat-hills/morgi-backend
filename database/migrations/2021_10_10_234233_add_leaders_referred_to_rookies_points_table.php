<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeadersReferredToRookiesPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rookies_points', function (Blueprint $table) {
            $table->integer('leaders_referred')->default(0);
        });

        Schema::table('rookies_points_histories', function (Blueprint $table) {
            $table->integer('leaders_referred')->after('login')->default(0);
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
            $table->dropColumn('leaders_referred');
        });

        Schema::table('rookies_points_histories', function (Blueprint $table) {
            $table->dropColumn('leaders_referred');
        });
    }
}
