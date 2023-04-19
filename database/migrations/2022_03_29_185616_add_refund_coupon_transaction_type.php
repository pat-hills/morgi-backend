<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddRefundCouponTransactionType extends Migration
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
        \App\Models\TransactionType::query()->create([
            'type' => 'not_refund_gift_with_coupon',
            'description_leader' => 'MOnthly Recurring GIft to <a href="/rookie-profile/{{rookie_id}}">{{rookie_full_name}}</a>, exchanged for coupon #{{coupon_id}}',
            'lang' => 'EN',
            'description_rookie' => 'MOnthly Recurring GIft from {{leader_full_name}}'
        ]);
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
