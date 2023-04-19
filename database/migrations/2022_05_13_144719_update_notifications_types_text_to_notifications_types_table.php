<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNotificationsTypesTextToNotificationsTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\NotificationType::query()
            ->where('type', 'leader_referred_rookie')
            ->first()
            ->update([
                'title' => 'Referred rookie is now available online!',
                'content' => 'Start your journey with <ref_username> by talking and elevating your connection with mentorship, advice or even gifts of cash. You decide! Click here to go the chat channel.'
            ]);


        \App\Models\NotificationType::query()
            ->where('type', 'leader_referred_rookie_welcome')
            ->first()
            ->update([
                'title' => 'Your referrer is notified that you are online!',
                'content' => 'Click here to go to your chat channel and start talking to <ref_username> and others, thanking them for inviting you, and showing them how serious you are about your chosen life path. They can even choose to gift you with cash!'
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \App\Models\NotificationType::query()
            ->where('type', 'leader_referred_rookie')
            ->first()
            ->update([
                'title' => 'Referred rookie is now available online!',
                'content' => 'Start your journey with <ref_username> with a gift that will open a chat channel between you both. Click here to view his profile.'
            ]);


        \App\Models\NotificationType::query()
            ->where('type', 'leader_referred_rookie_welcome')
            ->first()
            ->update([
                'title' => 'Your referrer is notified that you are online!',
                'content' => 'In the meantime, check out your dashboard for tips on how to make the most as a Rookie in Morgi, to make sure your profile stands our from the rest.'
            ]);
    }
}
