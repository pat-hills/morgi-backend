<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGiftNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\NotificationType::query()->create(['user_type' => 'rookie',
            'type' => 'rookie_new_gift', 'title' => 'Congratulations!',
            'content' => '<ref_username> just gifted you <amount_morgi> Morgi!']);

        \App\Models\NotificationType::query()->create(['user_type' => 'leader',
            'type' => 'leader_new_gift', 'title' => 'Congratulations!',
            'content' => 'You successfully gifted <ref_username> with <amount_morgi> Morgi!']);
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
