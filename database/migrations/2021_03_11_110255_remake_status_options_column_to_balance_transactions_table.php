<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RemakeStatusOptionsColumnToBalanceTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE balance_transactions CHANGE COLUMN status status ENUM('in_progress', 'paid', 'to_refund', 'refund_in_progress', 'refunded', 'error_to_refund') NOT NULL DEFAULT 'in_progress'");


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE balance_transactions CHANGE COLUMN status status ENUM('completed', 'refunded', 'to_refund', 'in_process') NOT NULL DEFAULT 'in_process'");
    }
}
