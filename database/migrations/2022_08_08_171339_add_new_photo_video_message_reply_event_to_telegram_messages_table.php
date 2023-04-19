<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use  App\Models\TelegramMessage;
class AddNewPhotoVideoMessageReplyEventToTelegramMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $messages = [
            [
                'type' => 'new_photo', 'media_type' => 'gif',
                'message' => "telegram_bot/welcome.gif",
                'order' => 1
            ],
            [
                'type' => 'new_photo', 'media_type' => 'text',
                'message' => "{{rookie_name}} has just sent you a new photo 💜📷 <a href='{{message_center}}'>Go to Chat now</a>",
                'order' => 2
            ],
            [
                'type' => 'new_video', 'media_type' => 'gif',
                'message' => "telegram_bot/welcome.gif",
                'order' => 1
            ],
            [
                'type' => 'new_video', 'media_type' => 'text',
                'message' => "{{rookie_name}} has just sent you a new video 💜🎥 <a href='{{message_center}}'>Go to Chat now</a>",
                'order' => 2
            ],
            [
                'type' => 'new_message', 'media_type' => 'gif',
                'message' => "telegram_bot/welcome.gif",
                'order' => 1
            ],
            [
                'type' => 'new_message', 'media_type' => 'text',
                'message' => "{{rookie_name}} has just sent you a new message 💜 <a href='{{message_center}}'>Go to Chat now</a>",
                'order' => 2
            ]
            ,
            [
                'type' => 'new_reply', 'media_type' => 'gif',
                'message' => "telegram_bot/welcome.gif",
                'order' => 1
            ],
            [
                'type' => 'new_reply', 'media_type' => 'text',
                'message' => "{{rookie_name}} has replied to your gift 🎁 💜 <a href='{{message_center}}'>Go to Chat now</a>",
                'order' => 2
            ]
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
