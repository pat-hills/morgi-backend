<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnToPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments_rookies', function (Blueprint $table) {
            //
            $table->renameColumn('refund_reason', 'note');
            $table->dropColumn('refund_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments_rookies', function (Blueprint $table) {
            //
            $table->renameColumn('note', 'refund_reason');
            $table->text('refund_date')->nullable();
        });
    }
}
