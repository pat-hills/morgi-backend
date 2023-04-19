<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBalanceTransactionsTable extends Migration
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
            $table->string('subscription_id')->nullable()->after('leader_id');
            $table->string('ccbill_failureReason')->nullable();
            $table->string('ccbill_failureCode')->nullable();
            $table->string('uuid')->nullable()->after('amount');
            $table->enum('status', ['in_progress','paid','to_refund','refund_in_progress','refunded','error_to_refund','failed'])->after('id');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('last_subscription_at');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('ccbill_failureReason')->nullable();
            $table->string('ccbill_failureCode')->nullable();
            $table->timestamp('last_subscription_at')->nullable()->after('subscription_at');
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
            $table->dropColumn('subscription_id');
            $table->dropColumn('ccbill_failureReason');
            $table->dropColumn('ccbill_failureCode');
            $table->dropColumn('uuid');
            $table->dropColumn('status');
        });

        Schema::table('balance_transactions', function (Blueprint $table) {
            $table->enum('status', ['in_progress','paid','to_refund','refund_in_progress','refunded','error_to_refund'])->after('id');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('ccbill_failureReason');
            $table->dropColumn('ccbill_failureCode');
        });
    }
}
