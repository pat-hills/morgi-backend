<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRookiesPointsHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('rookies_points_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('rookie_id');
            $table->integer('login')->default(0);
            $table->integer('photo')->default(0);
            $table->integer('description')->default(0);
            $table->integer('morgi')->default(0);
            $table->integer('leader_micromorgi_last_week')->default(0);
            $table->integer('leader_saves')->default(0);
            $table->integer('new_leader')->default(0);
            $table->integer('first_micro_morgi')->default(0);
            $table->integer('morgi_last_week')->default(0);
            $table->integer('micro_morgi_last_week')->default(0);
            $table->integer('party')->default(0);
            $table->integer('merch')->default(0);
            $table->integer('avg_response_time')->default(0);
            $table->integer('avg_first_contact')->default(0);
            $table->integer('amount_morgi_last_week')->default(0);
            $table->integer('amount_micro_morgi_last_week')->default(0);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rookies_points_histories');
    }
}
