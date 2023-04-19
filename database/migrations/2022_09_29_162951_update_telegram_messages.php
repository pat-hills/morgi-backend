<?php

use App\Models\TelegramMessage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTelegramMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        TelegramMessage::query()->where('type', 'welcome')
            ->where('order', 3)
            ->update([
                'message' => "Now, you can receive instant updates about new messages and gifts you receive!😄"
            ]);

        TelegramMessage::query()->whereIn('type', ['new_message', 'new_reply', 'new_photo', 'new_video', 'inactive_leader', 'inactive_leader_ping'])
            ->where('media_type', 'gif')
            ->delete();

        TelegramMessage::query()->whereIn('type', ['new_message', 'new_reply', 'new_photo', 'new_video', 'inactive_leader', 'inactive_leader_ping'])
            ->where('media_type', 'text')
            ->update([
                'order' => 1
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
