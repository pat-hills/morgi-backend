<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentinfoToPaymentsPlatformsRookieTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments_platforms_rookies', function (Blueprint $table) {
            $table->string('payment_info')->after('payment_platform_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments_platforms_rookies', function (Blueprint $table) {
            $table->dropColumn('payment_info');
        });
    }
}
