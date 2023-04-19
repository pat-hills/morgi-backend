<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentsPlatformsRookiesIdToPaymentsPlatformsRookiesHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments_platforms_rookies_histories', function (Blueprint $table) {
            //
            $table->bigInteger('payments_platforms_rookies_id')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments_platforms_rookies_histories', function (Blueprint $table) {
            //
            $table->dropColumn('payments_platforms_rookies_id');
        });
    }
}
