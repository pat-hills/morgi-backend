<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSomeNewAdminAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        \App\Models\User::where('email', 'amirr@top4.com')->update(['email' => 'amirr@morgi.org']);

        $users = [
            ['type' => 'admin', 'email' => 'paulinez@morgi.org', 'status' => 'accepted', 'username' => 'Paulinez',
                'gender_id' => 2, 'email_verified_at' => now(), 'active' => 1, 'password' => '$2y$10$bJ.j4U0fxojYraLcQvfTQOb9hJo5QlboSJnkXeyhzQpFHlDWlzCnq'],

            ['type' => 'admin', 'email' => 'jenniferl@morgi.org', 'status' => 'accepted', 'username' => 'Jenniferl',
                'gender_id' => 2, 'email_verified_at' => now(), 'active' => 1, 'password' => '$2y$10$p.wqPijAlAZEXax0QPCOx.5PZrfhiA3WYXe7F0TN3k/8Xon2lifgO'],
        ];

        foreach ($users as $user){
            \App\Models\User::create($user);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
