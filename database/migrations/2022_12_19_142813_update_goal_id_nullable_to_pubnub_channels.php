<?php

use App\Models\PubnubChannel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateGoalIdNullableToPubnubChannels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pubnub_channels', function (Blueprint $table) {
            $table->unsignedBigInteger('goal_id')->after('name')->nullable()->change();
        });

        PubnubChannel::query()
            ->where('goal_id', 0)
            ->update([
                'goal_id' => null
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
            $table->unsignedBigInteger('goal_id')->after('updated_at')->default(0)->nullable(false)->change();
        });
    }
}
