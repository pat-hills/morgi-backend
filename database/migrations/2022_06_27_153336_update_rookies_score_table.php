<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRookiesScoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rookies_score', function (Blueprint $table) {
            $table->unsignedInteger('leaders_first_subscription')->default(0)->after('first_micromorgi_gift_leaders');
            $table->unsignedInteger('converters_subscriptions')->default(0)->after('leaders_sending_micromorgi_last_seven_days');
            $table->unsignedInteger('time_to_subscribe')->default(0)->after('leaders_sending_micromorgi_last_seven_days');
        });

        Schema::table('rookies_stats', function (Blueprint $table) {
            $table->unsignedInteger('leaders_first_subscription')->default(0)->after('first_micromorgi_gift_leaders');
            $table->unsignedInteger('converters_subscriptions')->default(0)->after('leaders_sending_micromorgi_last_seven_days');
            $table->unsignedInteger('time_to_subscribe')->default(0)->after('leaders_sending_micromorgi_last_seven_days');
        });

        Schema::table('rookies', function (Blueprint $table) {
            $table->unsignedInteger('leaders_first_subscription')->default(0)->after('first_micromorgi_gift_leaders');
        });

        Schema::table('pubnub_channels', function (Blueprint $table) {
            $table->unsignedInteger('time_to_subscribe')->nullable()->after('rookie_first_message_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rookies_score', function (Blueprint $table) {
            $table->dropColumn('leaders_first_subscription');
            $table->dropColumn('converters_subscriptions');
            $table->dropColumn('time_to_subscribe');
        });

        Schema::table('rookies_stats', function (Blueprint $table) {
            $table->dropColumn('leaders_first_subscription')->default(0);
            $table->dropColumn('converters_subscriptions')->default(0);
            $table->dropColumn('time_to_subscribe')->default(0);
        });

        Schema::table('rookies', function (Blueprint $table) {
            $table->dropColumn('leaders_first_subscription');
        });

        Schema::table('pubnub_channels', function (Blueprint $table) {
            $table->dropColumn('time_to_subscribe');
        });
    }
}
