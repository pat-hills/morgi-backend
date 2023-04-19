<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddTransactionTypeEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE transactions_types CHANGE
            COLUMN type type ENUM('chat','gift','withdrawal','refund','bonus','withdrawal_rejected',
    'bought_micromorgi','rookie_block_leader','refund_bonus','chargeback','withdrawal_pending','fine','gift_with_coupon', 'not_refund_gift_with_coupon')
            NOT NULL");

        \App\Models\TransactionType::query()->where('id', 14)->update(['type' => 'not_refund_gift_with_coupon']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
