<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUselessInfoFromPaymentsPlatformsRookiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments_platforms_rookies', function (Blueprint $table) {
            $table->dropColumn('payment_information');
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
            $table->text('payment_information')->after('payment_platform_id');
        });
    }
}
