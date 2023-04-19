<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeBalanceTransactionIdNullableInRookiesWinnersHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rookies_winners_histories', function (Blueprint $table) {
            $table->bigInteger('balance_transaction_id')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rookies_winners_histories', function (Blueprint $table) {
            $table->bigInteger('balance_transaction_id')->nullable(false)->change();
        });
    }
}
