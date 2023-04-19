<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChannelSettingIdToPubnubChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pubnub_channels', function (Blueprint $table) {
            $table->unsignedBigInteger('channel_setting_id')->after('is_referral');
        });

        $default_value = \App\Models\PubnubChannelSetting::query()
            ->where('type', 'none')
            ->first();

        \App\Models\PubnubChannel::query()->update([
                'channel_setting_id' => $default_value->id
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pubnub_channels', function (Blueprint $table) {
            $table->dropColumn('channel_setting_id');
        });
    }
}
