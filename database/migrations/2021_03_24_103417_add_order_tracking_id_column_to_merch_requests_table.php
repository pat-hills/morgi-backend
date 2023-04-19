<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderTrackingIdColumnToMerchRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merch_requests', function (Blueprint $table) {
            //
            $table->bigInteger('order_tracking_id')->after('rookie_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('merch_requests', function (Blueprint $table) {
            //
            $table->dropColumn('order_tracking_id');
        });
    }
}
