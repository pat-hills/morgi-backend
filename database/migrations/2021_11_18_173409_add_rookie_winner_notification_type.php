<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRookieWinnerNotificationType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\NotificationType::query()->create([
            'user_type' => 'rookie', 'type' => "rookie_winner_lottery_info", 'title' => "Welcome to Morgi!",
            'content' => "In Morgi you can get money from other to get the help you need to start your life. Don't forget to log in ofter.
                Not only would this make it more likely your profile would be found by others, each day we randomly select 3 users to receive the Treasure Chest
                (a gift from us of 100 Morgis to your account, but you must claim it within 24 hours or it's gone"
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
