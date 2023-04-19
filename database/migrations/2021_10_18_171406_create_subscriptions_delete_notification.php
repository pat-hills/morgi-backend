<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsDeleteNotification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\NotificationType::query()
            ->create([
                'user_type' => 'leader',
                'type' => 'invalid_card_subscription_canceled',
                'title' => 'OH NO!',
                'content' => 'Your monthly recurring gift to <ref_username> has not been re-newed due to an invalid card.'
            ]);

        \App\Models\NotificationType::query()
            ->create([
                'user_type' => 'rookie',
                'type' => 'leader_canceled_subscription',
                'title' => 'OH NO!',
                'content' => 'Your monthly recurring gift from <ref_username> has not been re-newed.'
            ]);
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
