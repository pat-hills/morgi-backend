<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConverterMessagePingToTelegramMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\TelegramMessage::query()->create([
            'type' => 'converter_message_ping', 'media_type' => 'text',
            'message' => "💬{{leader_username}} just sent you a new message. <a href='{{message_center}}'>Go to chat channel!</a>",
            'order' => 1]);
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
