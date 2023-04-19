<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddToTransactionIdFieldInCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->renameColumn('transaction_id', 'from_transaction_id');
            $table->unsignedBigInteger('to_transaction_id')->nullable('true')->after('currency_value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->renameColumn('from_transaction_id', 'transaction_id');
            $table->dropColumn('to_transaction_id');
        });
    }
}
