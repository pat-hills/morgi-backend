<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRookiesWinnersHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rookies_winners_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('rookie_id');
            $table->integer('amount');
            $table->bigInteger('balance_transaction_id');
            $table->timestamp('win_at');
            $table->timestamp('seen_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rookies_winners_histories');
    }
}
