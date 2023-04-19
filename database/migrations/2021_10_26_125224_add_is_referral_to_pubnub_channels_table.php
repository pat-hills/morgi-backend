<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsReferralToPubnubChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pubnub_channels', function (Blueprint $table) {
            $table->boolean('is_referral')->default(false)->after('subscription_id');
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
            $table->dropColumn('is_referral');
        });
    }
}
