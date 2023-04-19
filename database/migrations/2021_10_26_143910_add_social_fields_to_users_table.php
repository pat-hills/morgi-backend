<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSocialFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('clicked_telegram_link')->default(false)->after('cookie_policy');
            $table->boolean('clicked_facebook_link')->default(false)->after('cookie_policy');
            $table->boolean('clicked_instagram_link')->default(false)->after('cookie_policy');
        });

        Schema::table('users_emails_sent', function (Blueprint $table) {
            $table->string('type')->change();
        });

        \App\Models\User::query()->update(['clicked_telegram_link' => true, 'clicked_facebook_link' => true]);
        \App\Models\Email::create(['type' => 'SOCIAL_REMINDER', 'sendgrid_id' => 'd-27054d5d093e462ebe45131e820654ac']);
        \App\Models\Email::create(['type' => 'VERIFY_EMAIL_REMINDER', 'sendgrid_id' => 'd-4a1534fcf99f47508738097191e4065f']);
        \App\Models\Email::create(['type' => 'USER_PHOTO_APPROVED_BY_ADMIN', 'sendgrid_id' => 'd-80e0adec19e64131a23bea53c176ff75']);
        \App\Models\Email::create(['type' => 'USER_DECLINED_ATTRIBUTES_BY_ADMIN', 'sendgrid_id' => 'd-075c43ceb5804b27a89d2f4235bf817b']);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        \App\Models\Email::query()->whereIn('type', ['SOCIAL_REMINDER', 'VERIFY_EMAIL_REMINDER', 'USER_PHOTO_APPROVED_BY_ADMIN', 'USER_DECLINED_ATTRIBUTES_BY_ADMIN'])->delete();

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('clicked_telegram_link');
            $table->dropColumn('clicked_facebook_link');
            $table->dropColumn('clicked_instagram_link');
        });
    }
}
