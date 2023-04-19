<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaderGoalProofNotification extends Migration
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
                'user_type' => 'leader',
                'type' => 'leader_goal_completed',
                'title' => 'Great News!',
                'content' => 'The proofs of your supported goal are now online!'
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
        \App\Models\NotificationType::query()->where('type', 'leader_goal_completed')->delete();
    }
}
