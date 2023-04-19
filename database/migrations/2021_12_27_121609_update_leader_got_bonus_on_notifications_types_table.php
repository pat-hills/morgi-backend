<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLeaderGotBonusOnNotificationsTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $notification = \App\Models\NotificationType::query()->where('type', 'leader_got_bonus')->first();
        $notification->update([
            'user_type' => 'both',
            'type' => 'user_got_bonus'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $notification = \App\Models\NotificationType::query()->where('type', 'user_got_bonus')->first();
        $notification->update([
            'user_type' => 'leader',
            'type' => 'leader_got_bonus'
        ]);
    }
}
