<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddGoalToTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE transactions CHANGE
            COLUMN type type ENUM('chat','gift','withdrawal','refund','bonus','withdrawal_rejected',
            'bought_micromorgi','withdrawal_pending','fine', 'goal')
            NOT NULL");

        DB::statement("ALTER TABLE transactions_types CHANGE
            COLUMN type type
            ENUM('chat','gift','withdrawal','refund','bonus','withdrawal_rejected','bought_micromorgi','rookie_block_leader',
            'refund_bonus','chargeback','withdrawal_pending','fine','gift_with_coupon','not_refund_gift_with_coupon', 'goal')
            NOT NULL");

        \App\Models\TransactionType::query()->create([
            'type' => 'goal',
            'description_leader' => 'Goal Micro Morgi gift to <a href="/{{rookie_username}}">{{rookie_full_name}}</a>',
            'lang' => 'EN'
        ]);

        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('goal_id')->nullable()->after('subscription_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE transactions CHANGE
            COLUMN type type ENUM('chat','gift','withdrawal','refund','bonus','withdrawal_rejected',
            'bought_micromorgi','withdrawal_pending','fine')
            NOT NULL");

        DB::statement("ALTER TABLE transactions_types CHANGE
            COLUMN type type
            ENUM('chat','gift','withdrawal','refund','bonus','withdrawal_rejected','bought_micromorgi','rookie_block_leader',
            'refund_bonus','chargeback','withdrawal_pending','fine','gift_with_coupon','not_refund_gift_with_coupon')
            NOT NULL");

        \App\Models\TransactionType::query()->where('type', 'goal')->delete();

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('goal_id');
        });
    }
}
