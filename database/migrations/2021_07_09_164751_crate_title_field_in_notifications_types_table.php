<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CrateTitleFieldInNotificationsTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\NotificationType::truncate();

        Schema::table('notifications_types', function (Blueprint $table) {
            $table->dropColumn('icon');
            $table->string('title')->nullable(true)->after('type');
        });

        $notifications = [
            ['user_type' => 'leader', 'type' => 'leader_login',
                'title' => 'Welcome to Morgi!', 'content' => 'Look for people you’d like to help and give them a monthly gift to get them started on their path'],

            ['user_type' => 'leader', 'type' => 'leader_first_gift_to_rookie',
                'title' => 'Y’re amazing!', 'content' => 'You’ve made your first monthly gift! It will be renewed each month on <event_at>, but you can cancel at any time.'],

            ['user_type' => 'leader', 'type' => 'leader_buy_micromorgi_package',
                'title' => 'Congratulations!', 'content' => 'You’ve bought a package of <amount_micromorgi> MicroMorgi worth <amount> <currency>!'],

            ['user_type' => 'leader', 'type' => 'leader_change_gift_amount',
                'title' => 'Please note!', 'content' => 'You’ve changed your monthly gift amount to <ref_username> from <old_amount> Morgi to <amount> Morgi'],

            ['user_type' => 'leader', 'type' => 'leader_renewed_gift',
                'title' => 'Renewed gift!', 'content' => 'Your monthly gift to Sally Molly for <amount_morgi> Morgi has been renewed for this month.'],

            ['user_type' => 'rookie', 'type' => 'rookie_login',
                'title' => 'Welcome to Morgi!', 'content' => 'Build your profile, and get the help you need to start on your path!'],

            ['user_type' => 'rookie', 'type' => 'rookie_first_gift_from_leader',
                'title' => 'Congratulations!', 'content' => 'You’ve received your very first gift! How exciting! Be sure to thank <ref_username>. For the <amount_morgi> Morgi they gifted you!'],

            ['user_type' => 'rookie', 'type' => 'rookie_receive_micromorgi',
                'title' => 'Congratulations!', 'content' => '<ref_username> sent you a gift of <amount_micromorgi> MicroMorgi']
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
        Schema::table('notifications_types', function (Blueprint $table) {
            $table->string('icon')->nullable(true);
            $table->dropColumn('title');
        });
    }
}
