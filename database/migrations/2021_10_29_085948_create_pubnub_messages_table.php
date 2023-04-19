<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePubnubMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pubnub_messages', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->unsignedBigInteger('channel_id');
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('receiver_id')->nullable(true);
            $table->timestamp('sent_at');
            $table->timestamps();
        });

        Schema::table('pubnub_channels', function (Blueprint $table) {
            $table->timestamp('rookie_first_message_at')->nullable(true)->after('name');
            $table->timestamp('leader_first_message_at')->nullable(true)->after('name');
            $table->unsignedInteger('avg_response_time')->nullable(true)->after('name');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('rookie_first_message_at');
            $table->dropColumn('leader_first_message_at');
            $table->dropColumn('avg_response_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pubnub_messages');

        Schema::table('pubnub_channels', function (Blueprint $table) {
            $table->dropColumn('rookie_first_message_at');
            $table->dropColumn('leader_first_message_at');
            $table->dropColumn('avg_response_time');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->timestamp('rookie_first_message_at')->nullable(true)->after('rookie_blocked_leader');
            $table->timestamp('leader_first_message_at')->nullable(true)->after('rookie_blocked_leader');
            $table->unsignedInteger('avg_response_time')->nullable(true)->after('rookie_blocked_leader');
        });
    }
}
