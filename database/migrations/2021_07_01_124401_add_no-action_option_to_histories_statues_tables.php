<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNoActionOptionToHistoriesStatuesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE videos_histories CHANGE COLUMN status status ENUM('to_check', 'approved', 'declined', 'no_action') NOT NULL DEFAULT 'to_check'");
        DB::statement("ALTER TABLE users_descriptions_histories CHANGE COLUMN status status ENUM('to_check', 'approved', 'declined', 'no_action') NOT NULL DEFAULT 'to_check'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE videos_histories CHANGE COLUMN status status ENUM('to_check', 'approved', 'declined') NOT NULL DEFAULT 'to_check'");
        DB::statement("ALTER TABLE users_descriptions_histories CHANGE COLUMN status status ENUM('to_check', 'approved', 'declined') NOT NULL DEFAULT 'to_check'");

    }
}
