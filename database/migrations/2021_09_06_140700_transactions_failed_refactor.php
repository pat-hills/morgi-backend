<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TransactionsFailedRefactor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions_failed', function (Blueprint $table) {
            $table->dropColumn('transaction_id');
            $table->bigInteger('subscription_id')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions_failed', function (Blueprint $table) {
            $table->dropColumn('subscription_id');
            $table->bigInteger('transaction_id')->after('id');
        });
    }
}
