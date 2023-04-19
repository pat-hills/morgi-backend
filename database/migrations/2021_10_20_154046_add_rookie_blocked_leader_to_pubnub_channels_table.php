<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRookieBlockedLeaderToPubnubChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pubnub_channels', function (Blueprint $table) {
            $table->boolean('rookie_blocked_leader')->default(false)->after('active');
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
            $table->dropColumn('rookie_blocked_leader');
        });
    }
}
