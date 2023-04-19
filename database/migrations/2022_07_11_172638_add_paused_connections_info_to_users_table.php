<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPausedConnectionsInfoToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('total_successful_paused_connections')->default(0)->after('total_subscriptions_count');
            $table->unsignedInteger('total_paused_connections')->default(0)->after('total_subscriptions_count');
        });

        Schema::table('rookies', function (Blueprint $table) {
            $table->dropColumn('total_successful_paused_connections');
            $table->dropColumn('total_paused_connections');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rookies', function (Blueprint $table) {
            $table->unsignedInteger('total_successful_paused_connections')->default(0)->after('leaders_first_subscription');
            $table->unsignedInteger('total_paused_connections')->default(0)->after('leaders_first_subscription');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('total_successful_paused_connections');
            $table->dropColumn('total_paused_connections');
        });
    }
}
