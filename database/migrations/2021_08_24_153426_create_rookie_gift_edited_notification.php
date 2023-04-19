<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRookieGiftEditedNotification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        \App\Models\NotificationType::query()->create(['user_type' => 'rookie',
            'type' => 'rookie_change_gift_amount', 'title' => 'Please note!',
            'content' => '<ref_username> changed monthly gift amount from <old_amount> Morgi to <amount> Morgi']);
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
