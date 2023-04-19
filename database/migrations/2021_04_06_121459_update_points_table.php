<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('points', function (Blueprint $table) {
            $table->dropColumn('morgi');
            $table->dropColumn('leader');
            $table->integer('leader_micromorgi_last_week')->default(0);
            $table->integer('avg_first_contact')->default(0);
            $table->integer('avg_response_time')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('points', function (Blueprint $table) {
            $table->integer('morgi')->default(0);
            $table->integer('leader')->default(0);
            $table->dropColumn('leader_micromorgi_last_week');
            $table->dropColumn('avg_first_contact');
            $table->dropColumn('avg_response_time');
        });
    }
}
