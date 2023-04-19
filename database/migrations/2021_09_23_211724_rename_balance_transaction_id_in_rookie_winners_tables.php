<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameBalanceTransactionIdInRookieWinnersTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rookies_winners_histories', function (Blueprint $table) {
            $table->renameColumn('balance_transaction_id', 'transaction_id');
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
            $table->renameColumn('transaction_id', 'balance_transaction_id');
        });
    }
}
