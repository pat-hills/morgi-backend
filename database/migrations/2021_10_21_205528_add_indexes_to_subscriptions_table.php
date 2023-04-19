<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->index(['leader_id', 'rookie_blocked_leader']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->index(['leader_id', 'rookie_blocked_leader', 'status']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->index(['leader_id', 'rookie_blocked_leader', 'status', 'last_subscription_at'], 'active_gifting');
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
            $table->dropIndex(['leader_id', 'rookie_blocked_leader']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex(['leader_id', 'rookie_blocked_leader', 'status']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex('active_gifting');
        });
    }
}
