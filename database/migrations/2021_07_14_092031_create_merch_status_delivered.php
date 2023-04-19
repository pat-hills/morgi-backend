<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateMerchStatusDelivered extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE merch_requests CHANGE COLUMN status status ENUM('pending','in_elaboration','to_sent','ready_to_sent','sent','canceled', 'delivered') NOT NULL DEFAULT 'pending'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE merch_requests CHANGE COLUMN status status ENUM('pending','in_elaboration','to_sent','ready_to_sent','sent','canceled') NOT NULL DEFAULT 'pending'");
    }
}
