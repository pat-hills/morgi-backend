<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTextTelegramBotNotification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $notification = \App\Models\NotificationType::query()
            ->where('type', 'telegram_bot')
            ->first();

        $content = str_replace('Leaders', 'Friends', $notification->content);
        $notification->update([
            'content' => $content
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $notification = \App\Models\NotificationType::query()
            ->where('type', 'telegram_bot')
            ->first();

        $content = str_replace('Friends', 'Leaders', $notification->content);
        $notification->update([
            'content' => $content
        ]);
    }
}
