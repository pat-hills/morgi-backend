<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertRookieDataToNotificationTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('notifications_types')->insert([
            'name'                              => 'rookie_login',
            'icon'                              => 'icon-5',
            'content_template'                  => 'Welcome to Morgi! Build your profile, and get the help you need to start on your path!',
            'content_template_key_name'         => 'rookie.login_notification',
        ]);

        DB::table('notifications_types')->insert([
            'name'                              => 'rookie_first_gift_from_leader',
            'icon'                              => 'icon-6',
            'content_template'                  => 'You’ve received your very first gift! How exciting! Be sure to thank <$leadername$>. For the <$amount$> Morgis they gifted you!',
            'content_template_key_name'         => 'rookie.rookie_first_gift_from_leader',
        ]);

        DB::table('notifications_types')->insert([
            'name'                              => 'rookie_receive_micromorgi',
            'icon'                              => 'icon-7',
            'content_template'                  => '<$username$> sent you a gift of <$num$> micromorgis',
            'content_template_key_name'         => 'rookie.rookie_receive_micromorgi',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('notifications_types')->where('name', 'like', 'rookie_%')->delete();
    }
}
