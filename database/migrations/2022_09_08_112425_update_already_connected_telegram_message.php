<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAlreadyConnectedTelegramMessage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\TelegramMessage::query()->where('type', 'already_connected')
            ->where('media_type', 'text')
            ->where('order', 1)
            ->update([
                'message' => "Your Telegram account is already connected to another Morgi account."
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
