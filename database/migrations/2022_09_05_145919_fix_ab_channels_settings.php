<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixAbChannelsSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $type_b = \App\Models\PubnubChannelSetting::query()->where('type', 'b')->first();
        if(!isset($type_b)){
            return;
        }

        $type_a = \App\Models\PubnubChannelSetting::query()->where('type', 'a')->first();
        if(!isset($type_a)){
            return;
        }

        \App\Models\PubnubChannel::query()->where('channel_setting_id', $type_b->id)
            ->update([
                'channel_setting_id' => $type_a->id
            ]);

        $type_b->delete();
        $type_a->update([
            'type' => 'a/b'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
