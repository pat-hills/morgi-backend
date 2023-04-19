<?php

use App\Models\TelegramMessage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewMessageInactivityToTelegramMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $messages = [
            ['type' => 'inactive_leader', 'media_type' => 'gif', 'message' => "telegram_bot/welcome.gif", 'order' => 1],

            ['type' => 'inactive_leader', 'media_type' => 'text', 'message' => "{{first_name}}, don't let Rookies wait! Those you havechosen to connect with are waiting for your message. Make their day ✨ <a href='{{message_center}}'>Go to Message Center now</a>", 'order' => 2],
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
