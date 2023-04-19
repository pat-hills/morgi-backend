<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeRookieBlockedLeaderNotNull extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->boolean('rookie_blocked_leader')->default(false)->nullable(false)->after('last_subscription_at')->change();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->boolean('rookie_blocked_leader')->default(false)->nullable(false)->after('refunded_by')->change();
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
            $table->boolean('rookie_blocked_leader')->default(false)->nullable(true)->after('last_subscription_at')->change();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->boolean('rookie_blocked_leader')->default(false)->nullable(true)->after('refunded_by')->change();
        });
    }
}
