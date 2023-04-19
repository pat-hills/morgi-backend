<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixInactiveLeaderMessage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\TelegramMessage::query()->where('type', 'inactive_leader')
            ->where('media_type', 'text')
            ->update([
                'message' => "{{first_name}}, don't let Rookies wait! Those you have chosen to connect with are waiting for your message. Make their day ✨ <a href='{{message_center}}'>Go to Message Center now</a>"
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
