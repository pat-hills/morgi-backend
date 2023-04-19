<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusColumnAndReasonRefundColumnToBalanceTransactionsTable extends Migration
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
            $table->enum('status', ['completed', 'refunded', 'to_refund', 'in_process'])->default('in_process')->after('currency_type');
            $table->string('refund_reason', 255)->nullable()->after('status');
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
            $table->dropColumn('status');
            $table->dropColumn('refund_reason');
        });
    }
}
