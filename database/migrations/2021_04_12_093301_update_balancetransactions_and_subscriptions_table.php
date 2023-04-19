<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBalancetransactionsAndSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('balance_transactions', function (Blueprint $table) {
            $table->dropColumn('uuid');
            $table->bigInteger('leader_payment_method_id')->nullable()->after('leader_id');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('ccbill_subscriptionId');
            $table->dropColumn('ccbill_clientAccnum');
            $table->dropColumn('ccbill_clientSubacc');
            $table->bigInteger('leader_payment_method_id')->nullable()->after('leader_id');
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
            $table->string('uuid')->nullable();
            $table->dropColumn('leader_payment_method_id');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('ccbill_subscriptionId')->nullable();
            $table->string('ccbill_clientAccnum')->nullable();
            $table->string('ccbill_clientSubacc')->nullable();
            $table->dropColumn('leader_payment_method_id');
        });
    }
}
