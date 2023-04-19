<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserDeclineNotificationToNotificationsTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        \App\Models\NotificationType::create([
            'user_type' => 'both',
            'type' => 'user_declined',
            'title' => 'OH NO!',
            'content' => '<reason>'
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \App\Models\NotificationType::where('type', 'user_declined')->first()->delete();
    }
}
