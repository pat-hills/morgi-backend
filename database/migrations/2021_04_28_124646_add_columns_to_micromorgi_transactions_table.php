<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToMicromorgiTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('micromorgi_transactions', function (Blueprint $table) {
            $table->enum('status', ['in_progress','paid','to_refund','refund_in_progress','refunded','error_to_refund','failed'])->default('in_progress')->after('id');
            $table->string('uuid')->nullable(true)->after('amount');
            $table->string('leader_payment_method_id')->nullable(true)->after('uuid');
            $table->string('refund_reason')->nullable(true)->after('uuid');
            $table->dateTime('refund_date')->nullable(true)->after('uuid');
            $table->string('ccbill_failureCode')->nullable(true)->after('uuid');
            $table->string('ccbill_failureReason')->nullable(true)->after('uuid');
            $table->string('ccbill_subscriptionId')->nullable(true)->after('uuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('micromorgi_transactions', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('uuid');
            $table->dropColumn('leader_payment_method_id');
            $table->dropColumn('refund_reason');
            $table->dropColumn('refund_date');
            $table->dropColumn('ccbill_failureCode');
            $table->dropColumn('ccbill_failureReason');
            $table->dropColumn('ccbill_subscriptionId');
        });
    }
}
