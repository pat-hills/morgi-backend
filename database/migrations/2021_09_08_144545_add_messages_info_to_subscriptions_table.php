<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMessagesInfoToSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->timestamp('leader_first_message_at')->nullable(true)->after('last_subscription_at');
            $table->timestamp('rookie_first_message_at')->nullable(true)->after('last_subscription_at');
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
            $table->dropColumn('leader_first_message_at');
            $table->dropColumn('rookie_first_message_at');
        });
    }
}
