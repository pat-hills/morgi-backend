<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdaptPubnubChannels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pubnub_channels', function (Blueprint $table) {
            $table->timestamp('last_activity_at')->after('is_referral')->useCurrent();
        });

        DB::statement('UPDATE pubnub_channels SET last_activity_at = created_at');

        $channels = \App\Models\PubnubChannel::query()->where(function ($query){
            $query->orWhereNotNull('leader_first_message_at')
                ->orWhereNotNull('rookie_first_message_at');
        })->get();

        foreach ($channels as $channel){
            $last_message = \App\Models\PubnubMessage::query()->where('channel_id', $channel->id)->latest('sent_at')->first();
            if(isset($last_message)){
                $channel->update([
                    'last_activity_at' => $last_message->sent_at
                ]);
            }
        }

        Schema::create('channels_reads_timetokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('channel_id');
            $table->string('timetoken');
            $table->timestamps();
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
            $table->dropColumn('last_activity_at');
        });

        Schema::dropIfExists('channels_reads_timetokens');
    }
}
