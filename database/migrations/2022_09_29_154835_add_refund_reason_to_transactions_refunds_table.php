<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRefundReasonToTransactionsRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions_refunds', function (Blueprint $table) {
            $table->text('refund_reason')->nullable(true)->after('admin_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions_refunds', function (Blueprint $table) {
            $table->dropColumn('refund_reason');
        });
    }
}
