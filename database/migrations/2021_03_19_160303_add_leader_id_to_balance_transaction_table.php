<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeaderIdToBalanceTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('balance_transactions', function (Blueprint $table) {
            $table->dropColumn('ref_user_id');
            $table->dropColumn('user_id');
            $table->bigInteger('rookie_id')->after('id');
            $table->bigInteger('leader_id')->after('rookie_id');
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
            $table->bigInteger('user_id')->after('id');
            $table->bigInteger('ref_user_id')->after('user_id');
            $table->dropColumn('rookie_id');
            $table->dropColumn('leader_id');
        });
    }
}
