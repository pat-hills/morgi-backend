<?php

use App\Models\TelegramMessage;
use App\Models\UserTelegramMessageSent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameTypeToTelegramMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        TelegramMessage::query()->where('type', 'converter_message_ping')->update(['type' => 'rookie_message_ping']);
        UserTelegramMessageSent::query()->where('type', 'converter_message_ping')->update(['type' => 'rookie_message_ping']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        TelegramMessage::query()->where('type', 'rookie_message_ping')->update(['type' => 'converter_message_ping']);
        UserTelegramMessageSent::query()->where('type', 'rookie_message_ping')->update(['type' => 'converter_message_ping']);
    }
}
