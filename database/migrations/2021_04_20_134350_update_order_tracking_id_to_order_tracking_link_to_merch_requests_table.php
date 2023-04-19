<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOrderTrackingIdToOrderTrackingLinkToMerchRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE merch_requests CHANGE COLUMN order_tracking_id order_tracking_link TEXT");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE merch_requests CHANGE COLUMN order_tracking_link order_tracking_id bigint");
    }
}
