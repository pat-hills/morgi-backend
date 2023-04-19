<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelegramMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telegram_messages', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('media_type');
            $table->text('message');
            $table->unsignedInteger('order');
            $table->timestamps();
        });

        $messages = [

            ['type' => 'invalid_token', 'media_type' => 'text', 'message' => "Oh noo! The provided token is invalid!

Please try to  click on the 'Join the Telegram Bot' link that appears on your main menu in Morgi,
or contact the <a href='{{customer_support_url}}'>customer support</a> team🙏", 'order' => 1],

            ['type' => 'disconnect', 'media_type' => 'text', 'message' => "You have disconnected yourself from the Telegram bot through Morgi. You can now connect from another Telegram account", 'order' => 1],

            ['type' => 'disconnect', 'media_type' => 'text', 'message' => "If this is a mistake, feel free to click start again and paste your hash key. You can find it here: <a href='{{front_bot_page}}'>{{front_bot_page}}</a> 🙌", 'order' => 2],

            ['type' => 'welcome', 'media_type' => 'text', 'message' => "Hello {{first_name}} and welcome to the Notification Bot!🤖", 'order' => 1],

            ['type' => 'welcome', 'media_type' => 'gif', 'message' => "https://media.giphy.com/media/mfGkfzHM3KfdI0OmIW/giphy.gif", 'order' => 2],

            ['type' => 'welcome', 'media_type' => 'text', 'message' => "Now, you can receive instant updates about the payments
you receive and one message at the end of each day about the number of unread messages in your account!😄", 'order' => 3],

            ['type' => 'welcome', 'media_type' => 'text', 'message' => "If you don't have your notifications switched ON for Telegram, we recommend you to do it now!
Go to your Settings > Notifications in order to make it happen!🙌", 'order' => 4],

            ['type' => 'already_connected', 'media_type' => 'text', 'message' => "Your Morgi account is already connected to another Telegram user.", 'order' => 1],

            ['type' => 'already_connected', 'media_type' => 'text', 'message' => "If you don't have access to your Telegram account, please check this page and disconnect your Morgi account
from your existing Telegram account so you can reconnect it again to this Telegram account as a new user!🙏 <a href='{{front_bot_page}}'>{{front_bot_page}}</a>", 'order' => 2],

            ['type' => 'first_gift', 'media_type' => 'gif', 'message' => "https://media.giphy.com/media/YJ5OlVLZ2QNl6/giphy.gif", 'order' => 1],
            ['type' => 'first_gift', 'media_type' => 'text', 'message' => "{{leader_username}} just gifted you {{amount}} Morgis for THE VERY FIRST TIME!🎊🎉🥳 <a href='{{channel_link}}'>Say Thank You, Now!</a>", 'order' => 2],

            ['type' => 'recurring_gift', 'media_type' => 'gif', 'message' => "https://media.giphy.com/media/YJ5OlVLZ2QNl6/giphy.gif", 'order' => 1],
            ['type' => 'recurring_gift', 'media_type' => 'text', 'message' => "{{leader_username}} just gifted you {{amount}} recurring Morgis, again!🙏🙌💪 <a href='{{channel_link}}'>Show Your Appreciation, Now!</a>", 'order' => 2],

            ['type' => 'micromorgi_received', 'media_type' => 'gif', 'message' => "https://media.giphy.com/media/YJ5OlVLZ2QNl6/giphy.gif", 'order' => 1],
            ['type' => 'micromorgi_received', 'media_type' => 'text', 'message' => "{{leader_username}} just gifted you {{amount}} recurring Micro Morgis! Keep the good job going!💪🏆🏅 <a href='{{channel_link}}'>Thank Your Leader, Now!</a>", 'order' => 2],
        ];

        \App\Models\TelegramMessage::query()->insert($messages);

        Schema::create('users_telegram_data_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('telegram_username')->nullable(true);
            $table->unsignedBigInteger('telegram_user_id')->nullable(true);
            $table->unsignedBigInteger('telegram_chat_id')->nullable(true);
            $table->timestamps();
        });

        Schema::create('users_telegram_messages_sent', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(true);
            $table->unsignedBigInteger('telegram_chat_id');
            $table->text('message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('telegram_messages');
        Schema::dropIfExists('users_telegram_data_history');
        Schema::dropIfExists('users_telegram_messages_sent');
    }
}
