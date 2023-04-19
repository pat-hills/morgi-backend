<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovedAtAndRejectedAtToPaymentsRookiesTable extends Migration
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
            $table->timestamp('approved_at')->nullable()->after('note');
            $table->timestamp('rejected_at')->nullable()->after('approved_at');
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
            $table->dropColumn('approved_at');
            $table->dropColumn('rejected_at');
        });
    }
}
