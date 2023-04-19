<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEnumInBalanceTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('balance_transactions', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('balance_transactions', function (Blueprint $table) {
            $table->enum('status', ['in_progress','paid','to_refund','refund_in_progress','refunded','error_to_refund','failed', 'failed_attempt'])->after('id');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('balance_transactions', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('balance_transactions', function (Blueprint $table) {
            $table->enum('status', ['in_progress','paid','to_refund','refund_in_progress','refunded','error_to_refund', 'failed'])->after('id');
        });

    }
}
