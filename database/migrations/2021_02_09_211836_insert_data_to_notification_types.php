<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertDataToNotificationTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*DB::table('notifications_types')->insert([
            'name'                              => 'leader_login',
            'icon'                              => 'icon-1',
            'content_template'                  => 'Welcome to Morgi! Look for people you’d like to help and give them a monthly gift to get them started on their path',
            'content_template_key_name'         => 'leader.login_notification',
        ]);

        DB::table('notifications_types')->insert([
            'name'                              => 'leader_first_gift_to_rookie',
            'icon'                              => 'icon-2',
            'content_template'                  => 'Y’re amazing! You’ve made your first monthly gift! It will be renewed each month on <$date$>, but you can cancel at any time.',
            'content_template_key_name'         => 'leader.first_gift_notification',
        ]);

        DB::table('notifications_types')->insert([
            'name'                              => 'leader_buy_micromorgi_package',
            'icon'                              => 'icon-3',
            'content_template'                  => 'You’ve bought a package of <$num$> MicroMorgi worth <$amount$> <$currency$>!',
            'content_template_key_name'         => 'leader.buy_micromorgi_notification',
        ]);

        DB::table('notifications_types')->insert([
            'name'                              => 'leader_change_gift_amount',
            'icon'                              => 'icon-4',
            'content_template'                  => 'You’ve changed your monthly gift amount to <$rookiename$> from <$amount$> Morgis to <newamount$> Morgis',
            'content_template_key_name'         => 'leader.change_gift_amount_notification',
        ]);*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //DB::table('notifications_types')->where('name', 'like', 'leader_%')->delete();
    }
}
