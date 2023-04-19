<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLeaderPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leaders_packages', function (Blueprint $table) {
            $table->renameColumn('balance_transaction_id', 'transaction_id');
        });

        Schema::table('leaders_packages_transactions', function (Blueprint $table) {
            $table->renameColumn('balance_transaction_id', 'transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leaders_packages', function (Blueprint $table) {
            $table->renameColumn('transaction_id', 'balance_transaction_id');
        });

        Schema::table('leaders_packages_transactions', function (Blueprint $table) {
            $table->renameColumn('transaction_id', 'balance_transaction_id');
        });
    }
}
