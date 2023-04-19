<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWasEverPausedToPubnubChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pubnub_channels', function (Blueprint $table) {
            $table->boolean('was_ever_paused')->after('is_paused');
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
            $table->dropColumn('was_ever_paused');
        });
    }
}
