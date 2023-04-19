<?php

use App\Services\Chat\Chat;
use Illuminate\Database\Migrations\Migration;

class AdaptUsersToPubnub extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $users = \App\Models\User::whereIn('type', ['rookie', 'leader'])->get();

        foreach ($users as $user){
            Chat::config($user->id)->userSignup($user);
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
