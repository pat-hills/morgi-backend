<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRefundTypeColumnAndRefundByColumnToBalanceTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('balance_transactions', function (Blueprint $table) {
            //
            $table->enum('refund_type', ['void', 'chargeback', 'refund'])->nullable()->after('refund_reason');
            $table->bigInteger('refund_by')->nullable()->after('refund_type');
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
            //
            $table->dropColumn('refund_type');
            $table->dropColumn('refund_by');
        });
    }
}
