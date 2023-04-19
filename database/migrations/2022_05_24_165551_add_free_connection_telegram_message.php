<?php

use App\Models\TelegramMessage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFreeConnectionTelegramMessage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        TelegramMessage::create([
            'type' => 'free_connection', 'media_type' => 'gif',
            'message' => "telegram_bot/welcome.gif",
            'order' => 1
        ]);

        TelegramMessage::create([
            'type' => 'free_connection', 'media_type' => 'text',
            'message' => "{{leader_username}} decided to connect with you!"
                . PHP_EOL .
                "A message from them is already waiting in your chat channel."
                . PHP_EOL .
                "<a href='{{message_center}}'>Say hello now!</a>",
            'order' => 2
        ]);
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
