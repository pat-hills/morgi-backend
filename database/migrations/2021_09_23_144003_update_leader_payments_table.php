<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLeaderPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leaders_payments', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('note');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('ccbill_failureReason');
            $table->dropColumn('ccbill_failureCode');
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
            $table->text('description')->nullable(true);
            $table->text('note')->nullable(true);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('ccbill_failureReason')->nullable(true);
            $table->string('ccbill_failureCode')->nullable(true);
        });
    }
}
