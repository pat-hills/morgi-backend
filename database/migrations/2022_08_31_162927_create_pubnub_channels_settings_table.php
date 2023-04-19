<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePubnubChannelsSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pubnub_channels_settings', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        $channels_settings = [
            ['type' => 'a', 'is_active' => false, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['type' => 'b', 'is_active' => false, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['type' => 'all', 'is_active' => false, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['type' => 'none', 'is_active' => true, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['type' => 'converters_only', 'is_active' => false, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];

        \Illuminate\Support\Facades\DB::table('pubnub_channels_settings')->insert($channels_settings);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pubnub_channels_settings');
    }
}
