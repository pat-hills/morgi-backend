<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HotfixUsersStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE users CHANGE COLUMN status status ENUM('pending','accepted','rejected','untrusted','blocked','new', 'deleted') NOT NULL DEFAULT 'pending'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE users CHANGE COLUMN status status ENUM('pending','accepted','rejected','untrusted','blocked','new') NOT NULL DEFAULT 'pending'");

    }
}
