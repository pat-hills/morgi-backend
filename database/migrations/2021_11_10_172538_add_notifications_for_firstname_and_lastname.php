<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotificationsForFirstnameAndLastname extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\NotificationType::create(['user_type' => 'rookie', 'type' => 'rookie_changed_first_name',
            'title' => 'Name changed!', 'content' => 'Your name was updated by customer support']);

        \App\Models\NotificationType::create(['user_type' => 'rookie', 'type' => 'rookie_changed_last_name',
            'title' => 'Surname changed!', 'content' => 'Your surname was updated by customer support']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        $first = \App\Models\NotificationType::query()
            ->where('type', 'rookie_changed_first_name')
            ->first();
        $first->delete();

        $last = \App\Models\NotificationType::query()
            ->where('type', 'rookie_changed_last_name')
            ->first();
        $last->delete();
    }
}
