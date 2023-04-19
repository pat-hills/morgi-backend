<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(User::query()->where('email', 'rond@morgi.org')->exists()){
            return;
        }

        $username = 'RonDeutch';
        if(User::query()->where('username', $username)->exists()){
            $username = 'RonD';
        }

        User::create([
            'type' => 'admin',
            'email' => 'rond@morgi.org',
            'status' => 'accepted',
            'username' => $username,
            'gender_id' => 1,
            'email_verified_at' => now(),
            'active' => 1,
            'password' => '$2y$10$pD51gvRJmYkZ/V81pbYqxOdTb9hd55vNs4VlN0xBlR4E4Qpr.9AIG'
        ]);
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
