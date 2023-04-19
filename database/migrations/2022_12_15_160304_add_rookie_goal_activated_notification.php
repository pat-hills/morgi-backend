<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRookieGoalActivatedNotification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\NotificationType::create([
            'user_type' => 'rookie',
            'type' => 'rookie_goal_activated',
            'title' => 'Goal approved!',
            'content' => 'Your goal has been approved by the admin and is now live and visible to Morgi Friends.']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \App\Models\NotificationType::query()
            ->where('type', 'rookie_goal_activated')
            ->delete();
    }
}
