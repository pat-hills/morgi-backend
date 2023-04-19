<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTransactionTableDueTheRefactor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leaders_payments', function (Blueprint $table) {
            $table->bigInteger('subscription_id')->nullable(true)->after('ip_address');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->bigInteger('subscription_id')->nullable(true)->after('transaction_type_id');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('photo_id');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->bigInteger('photo_id')->nullable(true);
            $table->dropColumn('ref_balance_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leaders_payments', function (Blueprint $table) {
            $table->dropColumn('subscription_id');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('subscription_id');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('photo_id');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->bigInteger('photo_id')->nullable(false);
            $table->bigInteger('ref_balance_transaction_id')->nullable(true);
        });
    }
}
