<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDollarsAmountInTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('withdrawal_reference');
            $table->dropColumn('amount_dollars');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->double('amount_dollars')->after('amount_micromorgi');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('amount_dollars');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->integer('amount_dollars')->after('amount_micromorgi');
        });
    }
}
