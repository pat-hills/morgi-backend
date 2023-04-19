<?php

use App\Models\TelegramMessage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixTelegramBotMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        TelegramMessage::query()
            ->where('type', 'micromorgi_received')
            ->where('media_type', 'text')
            ->update(['message' => "{{leader_username}} just gifted you {{amount}} Micro Morgis! Keep the good job going!💪🏆🏅 <a href='{{channel_link}}'>Thank Your Leader, Now!</a>"]);

        TelegramMessage::query()
            ->where('type', 'first_gift')
            ->where('media_type', 'gif')
            ->update(['message' => "telegram_bot/first_gift.gif"]);

        TelegramMessage::query()
            ->where('type', 'welcome')
            ->where('media_type', 'gif')
            ->update(['message' => "telegram_bot/welcome.gif"]);

        TelegramMessage::query()
            ->where('type', 'micromorgi_received')
            ->where('media_type', 'gif')
            ->update(['message' => "telegram_bot/micromorgi_received.gif"]);

        TelegramMessage::query()
            ->where('type', 'recurring_gift')
            ->where('media_type', 'gif')
            ->update(['message' => "telegram_bot/recurring_gift.gif"]);
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
