<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaderBlockedRookieNotification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\NotificationType::query()->create([
            'user_type' => 'rookie', 'type' => 'leader_blocked_rookie', 'title' => 'A Leader has blocked you!',
            'content' => 'We are sorry to see that <ref_username> has decided to end their connection with you. Your path to greatness lies with other Leaders!'
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
