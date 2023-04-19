<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotificationsTypesToNotificationsTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $notifications = [
            ['user_type' => 'leader', 'type' => 'leader_free_subscription',
                'title' => 'You have a new contact!', 'content' => 'We hope you and <ref_username> will enjoy a fruitful connection. Click here and go to your chat channel now to start talking.'],

            ['user_type' => 'rookie', 'type' => 'rookie_free_subscription',
                'title' => 'Someone has chosen to connect with you!', 'content' => 'We hope you and <ref_username> will enjoy a fruitful connection. Click here and go to your chat channel now to start talking.'],
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
            ->whereIn('type', ['leader_free_subscription', 'rookie_free_subscription'])
            ->delete();

    }
}
