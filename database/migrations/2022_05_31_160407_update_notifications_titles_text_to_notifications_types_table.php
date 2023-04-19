<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNotificationsTitlesTextToNotificationsTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\NotificationType::query()
            ->where('type', 'rookie_free_subscription')
            ->first()
            ->update([
                'title' => 'You have a new connection!'
            ]);

        \App\Models\NotificationType::query()
            ->where('type', 'leader_free_subscription')
            ->first()
            ->update([
                'title' => 'You have a new connection!'
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
            ->where('type', 'rookie_free_subscription')
            ->first()
            ->update([
                'title' => 'Someone has chosen to connect with you!'
            ]);

        \App\Models\NotificationType::query()
            ->where('type', 'leader_free_subscription')
            ->first()
            ->update([
                'title' => 'You have a new contact!'
            ]);
    }
}
