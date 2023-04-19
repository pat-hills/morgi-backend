<?php

use App\Models\TelegramMessage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDailyReminderTelegramNotification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        TelegramMessage::create([
            'type' => 'unread_messages_reminder', 'media_type' => 'text',
            'message' => "Good evening {{rookie_first_name}} 😊
Your account has {{unread_messages}} unread messages waiting for you in the messages center.
Continue to <a href='{{message_center}}'>Message Center</a> Now!",
            'order' => 1]);

        Schema::table('users_telegram_messages_sent', function (Blueprint $table) {
            $table->string('type')->after('id');
            $table->unsignedInteger('unread_messages')->nullable(true)->after('message');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_telegram_messages_sent', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('unread_messages');
        });
    }
}
