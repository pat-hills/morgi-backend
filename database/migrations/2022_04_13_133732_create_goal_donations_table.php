<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoalDonationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goal_donations', function (Blueprint $table) {
            $table->id();
            $table->float('amount')->comment('Amount in micro-morgie within the constraint of goaltypes\' min max value');
            $table->enum('currency_type', ['morgi', 'micro_morgi'])->default('micro_morgi');
            $table->unsignedBigInteger('leader_id');
            $table->unsignedBigInteger('goal_id');
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
        Schema::dropIfExists('goal_donations');
    }
}
