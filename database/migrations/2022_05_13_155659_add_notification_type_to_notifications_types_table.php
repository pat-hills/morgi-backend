<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotificationTypeToNotificationsTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\NotificationType::create([
            'user_type' => 'leader',
            'type' => 'leader_pause_connection',
            'title' => 'Rookie paused your chat channel.',
            'content' => 'This might happen when <ref_username> is expecting a Monthly Recurring Gift of cash to open the chat channel again and resume the connection.'
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
            ->where('type', 'leader_pause_connection')
            ->first()
            ->delete();
    }
}
