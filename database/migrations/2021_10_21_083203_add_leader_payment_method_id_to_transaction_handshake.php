<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeaderPaymentMethodIdToTransactionHandshake extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions_handkshake', function (Blueprint $table) {
            $table->unsignedBigInteger('leader_payment_method_id')->nullable(true)->after('leader_payment_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions_handkshake', function (Blueprint $table) {
            $table->dropColumn('leader_payment_method_id');
        });
    }
}
