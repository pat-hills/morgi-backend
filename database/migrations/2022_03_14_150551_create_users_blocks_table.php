<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_blocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_user_id');
            $table->unsignedBigInteger('to_user_id');
            $table->timestamps();
            $table->softDeletes();
        });

        $rookies_blocks = \Illuminate\Support\Facades\DB::table('rookies_blocks')->get();
        foreach ($rookies_blocks as $rookie_block){
            \App\Models\UserBlock::query()->create([
                'from_user_id' => $rookie_block->rookie_id,
                'to_user_id' => $rookie_block->leader_id
            ]);
        }

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->renameColumn('rookie_blocked_leader', 'user_block_id');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('rookie_blocked_leader', 'user_block_id');
        });

        Schema::table('pubnub_channels', function (Blueprint $table) {
            $table->renameColumn('rookie_blocked_leader', 'user_block_id');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->unsignedBigInteger('user_block_id')->nullable()->default(null)->change();
        });
        \App\Models\Subscription::query()->whereNotNull('user_block_id')->update(['user_block_id' => null]);

        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('user_block_id')->nullable()->default(null)->change();
        });
        \App\Models\Transaction::query()->whereNotNull('user_block_id')->update(['user_block_id' => null]);

        Schema::table('pubnub_channels', function (Blueprint $table) {
            $table->unsignedBigInteger('user_block_id')->nullable()->default(null)->change();
        });
        \App\Models\PubnubChannel::query()->whereNotNull('user_block_id')->update(['user_block_id' => null]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_blocks');

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->boolean('user_block_id')->default(true)->change();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->boolean('user_block_id')->default(true)->change();
        });

        Schema::table('pubnub_channels', function (Blueprint $table) {
            $table->boolean('user_block_id')->default(true)->change();
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->renameColumn('user_block_id', 'rookie_blocked_leader');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('user_block_id', 'rookie_blocked_leader');
        });

        Schema::table('pubnub_channels', function (Blueprint $table) {
            $table->renameColumn('user_block_id', 'rookie_blocked_leader');
        });
    }
}
