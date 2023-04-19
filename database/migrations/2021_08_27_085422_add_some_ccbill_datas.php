<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeCcbillDatas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leaders_ccbill_data', function (Blueprint $table) {
            $table->string('billingCountry')->nullable(true)->after('error');
        });

        Schema::table('leaders_payments', function (Blueprint $table) {
            $table->string('payment_country')->nullable(true)->after('note');
            $table->string('ccbill_transactionId')->nullable(true)->after('note');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('signup_country_id')->default(239)->after('cookie_policy');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leaders_ccbill_data', function (Blueprint $table) {
            $table->dropColumn('billingCountry');
        });

        Schema::table('leaders_payments', function (Blueprint $table) {
            $table->dropColumn('payment_country');
            $table->dropColumn('ccbill_transactionId');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('signup_country_id');
        });
    }
}
