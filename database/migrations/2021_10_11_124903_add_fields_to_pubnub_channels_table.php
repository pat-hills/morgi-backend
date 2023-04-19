<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToPubnubChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pubnub_channels', function (Blueprint $table) {
            $table->string('type')->after('id');
            $table->unsignedBigInteger('subscription_id')->nullable(true)->after('active');
            $table->unsignedBigInteger('users_referral_emails_sent_id')->nullable(true)->after('active');
            $table->unsignedBigInteger('rookie_id')->nullable(true)->after('active');
            $table->unsignedBigInteger('leader_id')->nullable(true)->after('active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pubnub_channels', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('subscription_id');
            $table->dropColumn('users_referral_emails_sent_id');
            $table->dropColumn('rookie_id');
            $table->dropColumn('leader_id');
        });
    }
}
