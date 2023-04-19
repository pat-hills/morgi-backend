<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingNotification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $notification = [
            [
                'user_type' => 'rookie',
                'type' => 'rookie_goal_amount_reached',
                'title' => 'Congratulations!',
                'content' => 'Your goal has reached enough donations!'
            ]
        ];

        \App\Models\NotificationType::insert($notification);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \App\Models\NotificationType::query()->where('type', 'rookie_goal_amount_reached')->delete();
    }
}
