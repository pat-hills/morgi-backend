<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRefundByColumnToLeadersPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leaders_payments', function (Blueprint $table) {
            //
            $table->bigInteger('refund_by')->nullable()->after('refund_date');
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
            //
            $table->dropColumn('refund_by');
        });
    }
}
