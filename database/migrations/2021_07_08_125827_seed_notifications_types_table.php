<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeedNotificationsTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        \App\Models\NotificationType::truncate();

        $notifications = [
            ['user_type' => 'leader', 'type' => 'leader_login',
                'icon' => 'icon-1', 'content' => 'Welcome to Morgi! Look for people you’d like to help and give them a monthly gift to get them started on their path'],

            ['user_type' => 'leader', 'type' => 'leader_first_gift_to_rookie',
                'icon' => 'icon-2', 'content' => 'Y’re amazing! You’ve made your first monthly gift! It will be renewed each month on <event_at>, but you can cancel at any time.'],

            ['user_type' => 'leader', 'type' => 'leader_buy_micromorgi_package',
                'icon' => 'icon-3', 'content' => 'You’ve bought a package of <amount_micromorgi> MicroMorgi worth <amount> <currency>!'],

            ['user_type' => 'leader', 'type' => 'leader_change_gift_amount',
                'icon' => 'icon-4', 'content' => 'You’ve changed your monthly gift amount to <ref_username> from <old_amount> Morgi to <amount> Morgi'],

            ['user_type' => 'rookie', 'type' => 'rookie_login',
                'icon' => 'icon-5', 'content' => 'Welcome to Morgi! Build your profile, and get the help you need to start on your path!'],

            ['user_type' => 'rookie', 'type' => 'rookie_first_gift_from_leader',
                'icon' => 'icon-6', 'content' => 'You’ve received your very first gift! How exciting! Be sure to thank <ref_username>. For the <amount_morgi> Morgi they gifted you!'],

            ['user_type' => 'rookie', 'type' => 'rookie_receive_micromorgi',
                'icon' => 'icon-7', 'content' => '<ref_username> sent you a gift of <amount_micromorgi> MicroMorgi']
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
        \App\Models\NotificationType::truncate();

    }
}
