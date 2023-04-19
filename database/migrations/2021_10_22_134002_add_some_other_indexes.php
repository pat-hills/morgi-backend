<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeOtherIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rookies_winners_histories', function (Blueprint $table) {
            $table->index(['win_at']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->index(['rookie_id', 'created_at']);
            $table->index(['leader_id', 'leader_payment_id']);
            $table->index(['leader_id', 'leader_payment_id', 'created_at'], 'leader_transactions');
            $table->index(['rookie_id', 'created_at', 'type']);
            $table->index(['rookie_id', 'created_at', 'type', 'refund_type'], 'rookie_transactions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rookies_winners_histories', function (Blueprint $table) {
            $table->dropIndex(['win_at']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['rookie_id', 'created_at']);
            $table->dropIndex(['leader_id', 'leader_payment_id']);
            $table->dropIndex('leader_transactions');
            $table->dropIndex(['rookie_id', 'created_at', 'type']);
            $table->dropIndex('rookie_transactions');
        });
    }
}
