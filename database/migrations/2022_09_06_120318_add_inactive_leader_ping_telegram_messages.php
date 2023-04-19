<?php

use App\Models\TelegramMessage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInactiveLeaderPingTelegramMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $messages = [
            ['type' => 'inactive_leader_ping', 'media_type' => 'gif', 'message' => "telegram_bot/welcome.gif", 'order' => 1],
            ['type' => 'inactive_leader_ping', 'media_type' => 'text', 'message' => "{{first_name}}, you have {{messages_count}} messages waiting from {{rookies_names}} you have chosen to connect with. They are waiting for your reply💜🙌 <a href='{{message_center}}'>Go to Message Center now</a>", 'order' => 2],
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
        //
    }
}
