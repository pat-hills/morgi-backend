<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixTransactionsTypesLinks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\TransactionType::query()->where('type', 'chat')->update([
            'description_leader' => 'Gift Micro Morgi on chat to <a href="/{{rookie_username}}">{{rookie_full_name}}</a>'
        ]);

        \App\Models\TransactionType::query()->where('type', 'gift')->update([
            'description_leader' => 'MOnthly Recurring GIft to <a href="/{{rookie_username}}">{{rookie_full_name}}</a>'
        ]);

        \App\Models\TransactionType::query()->where('type', 'refund')->update([
            'description_leader' => 'System refund following an error to <a href="/{{rookie_username}}">{{rookie_full_name}} (#{{referal_internal_id}})</a>'
        ]);

        \App\Models\TransactionType::query()->where('type', 'rookie_block_leader')->update([
            'description_leader' => 'Canceled connection with <a href="/{{rookie_username}}">{{rookie_full_name}}</a> refund'
        ]);

        \App\Models\TransactionType::query()->where('type', 'chargeback')->update([
            'description_leader' => 'System refund following an error to <a href="/{{rookie_username}}">{{rookie_full_name}} (#{{referal_internal_id}})</a>'
        ]);

        \App\Models\TransactionType::query()->where('type', 'gift_with_coupon')->update([
            'description_leader' => 'MOnthly Recurring GIft to <a href="/{{rookie_username}}">{{rookie_full_name}}</a>, exchanged for coupon #{{coupon_id}}'
        ]);

        \App\Models\TransactionType::query()->where('type', 'not_refund_gift_with_coupon')->update([
            'description_leader' => 'MOnthly Recurring GIft to <a href="/{{rookie_username}}">{{rookie_full_name}}</a>, exchanged for coupon #{{coupon_id}}'
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
