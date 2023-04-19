<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RanameChannelNameToChannelIdToPubnubBroadcastsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pubnub_broadcasts', function (Blueprint $table) {
            $table->dropColumn('channel_name');
            $table->unsignedBigInteger('channel_id')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pubnub_broadcasts', function (Blueprint $table) {
            $table->string('channel_name')->after('id');
            $table->dropColumn('channel_id');
        });
    }
}
