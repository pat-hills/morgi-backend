<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDollarAmountToMicromorgiTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('micromorgi_transactions', function (Blueprint $table) {
            $table->double('dollar_amount')->after('amount');
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
            $table->dropColumn('dollar_amount');
        });
    }
}
