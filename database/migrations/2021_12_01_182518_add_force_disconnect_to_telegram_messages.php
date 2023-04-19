<?php

use App\Models\TelegramMessage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForceDisconnectToTelegramMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        TelegramMessage::create([
            'type' => 'force_disconnect', 'media_type' => 'text',
            'message' => "Hey {{rookie_first_name}} ✋
It doesn't seem like you are engaged with the bot, so we are unsubscribing you from this service - we don't want to unnecessarily message you!😊",
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
