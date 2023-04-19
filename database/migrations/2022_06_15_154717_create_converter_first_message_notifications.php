<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConverterFirstMessageNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        \App\Models\NotificationType::query()->create(['user_type' => 'leader',
            'type' => 'converter_first_message', 'title' => 'Your Lucky Match just sent you a message!',
            'content' => 'A message from <ref_username>, is waiting for your reply. We wish you a fruitful connection!']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \App\Models\NotificationType::query()->where('type', 'converter_first_message')->delete();
    }
}
