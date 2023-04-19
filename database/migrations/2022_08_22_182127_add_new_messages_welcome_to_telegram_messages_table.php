<?php

use App\Models\TelegramMessage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewMessagesWelcomeToTelegramMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $messages = [
            ['type' => 'welcome-leader', 'media_type' => 'text', 'message' => "Hello {{first_name}} and welcome to the Notification Bot!🤖", 'order' => 1],

            ['type' => 'welcome-leader', 'media_type' => 'gif', 'message' => "telegram_bot/welcome.gif", 'order' => 2],

            ['type' => 'welcome-leader', 'media_type' => 'text', 'message' => "If your Telegram notifications are not turned ON, we recommend doing it now - Just go to Settings > Notifications and make it happen!🙌", 'order' => 3],
        ];

        TelegramMessage::query()->upsert($messages, ['type', 'media_type']);
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
