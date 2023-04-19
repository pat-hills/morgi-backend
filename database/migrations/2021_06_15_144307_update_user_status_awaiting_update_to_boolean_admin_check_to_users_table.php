<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateUserStatusAwaitingUpdateToBooleanAdminCheckToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::statement("ALTER TABLE users CHANGE COLUMN status status ENUM('pending', 'accepted', 'rejected', 'untrusted', 'blocked', 'new') NOT NULL DEFAULT 'pending'");

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('admin_check')->default(0)->after('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE users CHANGE COLUMN status status ENUM('pending', 'accepted', 'rejected', 'untrusted', 'blocked', 'awaiting_update', 'new') NOT NULL DEFAULT 'pending'");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('admin_check');
        });
    }
}
