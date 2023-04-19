<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCanceledAndWaitingOptionsToOrdersTrackingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE orders_tracking CHANGE COLUMN status status ENUM('waiting', 'in_elaboration', 'in_transit', 'delivered', 'canceled') NOT NULL DEFAULT 'in_elaboration'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE orders_tracking CHANGE COLUMN status status ENUM('in_elaboration', 'in_transit', 'delivered') NOT NULL DEFAULT 'in_elaboration'");

    }
}
