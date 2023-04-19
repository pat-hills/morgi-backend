<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePointsLogic extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('points', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->integer('login')->default(0);
            $table->integer('photo')->default(0);
            $table->integer('description')->default(0);
            $table->integer('morgi')->default(0);
            $table->integer('micro_morgi')->default(0);
            $table->integer('leader')->default(0);
            $table->integer('leader_saves')->default(0);
            $table->integer('new_leader')->default(0);
            $table->integer('first_micro_morgi')->default(0);
            $table->integer('morgi_last_week')->default(0);
            $table->integer('micro_morgi_last_week')->default(0);
            $table->integer('party')->default(0);
            $table->integer('merch')->default(0);
        });

        Schema::table('rookies', function (Blueprint $table) {
            $table->dropColumn('points');
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
            $table->integer('points')->default(0);
        });
        Schema::dropIfExists('points');

    }
}
