<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAvgResponseTimeToSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->integer('avg_response_time')->nullable(true)->after('leader_first_message_at');
        });

        Schema::table('rookies_points', function (Blueprint $table) {
            $table->integer('avg_response_time_minutes')->default(0)->after('avg_first_contact');
        });

        Schema::table('rookies_points_histories', function (Blueprint $table) {
            $table->integer('avg_response_time_minutes')->default(0)->after('avg_first_contact');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('avg_response_time');
        });

        Schema::table('rookies_points', function (Blueprint $table) {
            $table->dropColumn('avg_response_time_minutes');
        });

        Schema::table('rookies_points_histories', function (Blueprint $table) {
            $table->dropColumn('avg_response_time_minutes');
        });
    }
}
