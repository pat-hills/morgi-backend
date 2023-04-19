<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaderReferRookieNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\NotificationType::query()->create(['user_type' => 'leader',
            'type' => 'leader_referred_rookie', 'title' => 'Referred rookie is now available online!',
            'content' => 'Start your journey with <ref_username> with a gift that will open a chat channel between you both. Click here to view his profile.'
        ]);

        \App\Models\NotificationType::query()->create(['user_type' => 'rookie',
            'type' => 'leader_referred_rookie_welcome', 'title' => 'Your referrer is notified that you are online!',
            'content' => 'In the meantime, check out your dashboard for tips on how to make the most as a Rookie in Morgi, to make sure your profile stands our from the rest.'
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
