<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGoalNotificationsTypesToNotificationsTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $notifications = [
            ['user_type' => 'rookie', 'type' => 'rookie_goal_approved',
                'title' => 'Congratulations!', 'content' => 'Your goal has been approved!'],

            ['user_type' => 'rookie', 'type' => 'rookie_goal_cancelled',
                'title' => 'OH NO!', 'content' => 'Unfortunately, your goal has been declined due to grave violations of our terms and conditions. You will need to create a new goal. Reason: <reason>'],

            ['user_type' => 'rookie', 'type' => 'rookie_goal_suspended',
                'title' => 'OH NO!', 'content' => 'Your goal has been suspended due to violations of our terms and conditions. Please edit the goal parameters to reactivate it again. Reason: <reason>'],

            ['user_type' => 'rookie', 'type' => 'rookie_goal_proof_approved',
                'title' => 'Congratulations!', 'content' => 'Your goal proof has been approved!'],

            ['user_type' => 'rookie', 'type' => 'rookie_goal_proof_declined',
                'title' => 'OH NO!', 'content' => 'Unfortunately, your goal proof has been declined. Please try again with a new proof. Reason: <reason>'],
        ];

        \App\Models\NotificationType::insert($notifications);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \App\Models\NotificationType::query()
            ->whereIn('type', ['rookie_goal_approved', 'rookie_goal_cancelled', 'rookie_goal_suspended', 'rookie_goal_proof_approved', 'rookie_goal_proof_declined'])
            ->delete();
    }
}
