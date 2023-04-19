<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CreateChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pubnub_channels', function (Blueprint $table) {
            $table->id();
            $table->string('display_name');
            $table->string('name');
            $table->string('category');
            $table->boolean('active')->default(false);
            $table->timestamps();
        });

        Schema::create('pubnub_channels_users', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('channel_id');
            $table->bigInteger('user_id');
        });

        Schema::create('pubnub_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->bigInteger('user_id');
            $table->timestamps();
        });

        Schema::create('pubnub_groups_channels', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('group_id');
            $table->bigInteger('channel_id');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('pubnub_uuid')->after('last_activity_at');
        });

        User::query()->update(['pubnub_uuid' => Str::orderedUuid()]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pubnub_channels');
        Schema::dropIfExists('pubnub_channels_users');
        Schema::dropIfExists('pubnub_groups');
        Schema::dropIfExists('pubnub_groups_channels');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('pubnub_uuid');
        });
    }
}
