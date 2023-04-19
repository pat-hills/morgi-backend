<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddGoalWithdrawTransactionType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE transactions_types CHANGE COLUMN type type ENUM('chat','gift','withdrawal','refund','bonus','withdrawal_rejected','bought_micromorgi','rookie_block_leader','refund_bonus','chargeback','withdrawal_pending','fine','gift_with_coupon','not_refund_gift_with_coupon','goal','goal_withdraw') NOT NULL");
        DB::statement("ALTER TABLE transactions CHANGE COLUMN type type ENUM('chat','gift','withdrawal','refund','bonus','withdrawal_rejected','bought_micromorgi','withdrawal_pending','fine','goal', 'goal_withdraw') NOT NULL");

        \App\Models\TransactionType::query()->create([
            'type' => 'goal_withdraw',
            'description_leader' => null,
            'description_rookie' => 'Withdraw of goal <a href="/{{goal_id}}">{{goal_name}}</a>'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE transactions_types CHANGE COLUMN type type ENUM('chat','gift','withdrawal','refund','bonus','withdrawal_rejected','bought_micromorgi','rookie_block_leader','refund_bonus','chargeback','withdrawal_pending','fine','gift_with_coupon','not_refund_gift_with_coupon','goal') NOT NULL");
        DB::statement("ALTER TABLE transactions CHANGE COLUMN type type ENUM('chat','gift','withdrawal','refund','bonus','withdrawal_rejected','bought_micromorgi','withdrawal_pending','fine','goal') NOT NULL");
    }
}
