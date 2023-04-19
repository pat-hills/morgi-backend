<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSubscriptionsTableAddPausedToStatusEnumValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `subscriptions` CHANGE `status` `status` ENUM('active','canceled','unsufficent_funds','pending','failed','paused') NOT NULL DEFAULT 'pending';");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `subscriptions` CHANGE `status` `status` ENUM('active','canceled','unsufficent_funds','pending','failed') NOT NULL DEFAULT 'pending';");
    }
}
