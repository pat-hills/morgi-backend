<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRefundLogicToRookiePayments extends Migration
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
            $table->string('admin_id')->after('amount')->nullable();
            $table->text('refund_reason')->after('admin_id')->nullable();
            $table->text('refund_date')->after('refund_reason')->nullable();
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
            $table->dropColumn('admin_id');
            $table->dropColumn('refund_reason');
            $table->dropColumn('refund_date');
        });
    }
}
