<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDeleteProfileNotificationsTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\NotificationType::create(['user_type' => 'rookie', 'type' => 'leader_deleted_account',
            'title' => 'OH NO!', 'content' => 'Unknown deleted their profile. Any future Morgi from this Leader will be stopped.']);

        \App\Models\NotificationType::create(['user_type' => 'leader', 'type' => 'rookie_deleted_account',
            'title' => 'OH NO!', 'content' => 'Unknown deleted their profile. Any future Morgi to this Rookie will be stopped.']);
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
