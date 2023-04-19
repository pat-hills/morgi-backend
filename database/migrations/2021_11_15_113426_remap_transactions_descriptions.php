<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemapTransactionsDescriptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\TransactionType::query()->where('type', 'chat')->update([
            'description_leader' => 'Gift Micro Morgi on chat to <a href="/{{rookie_username}}">{{rookie_full_name}}</a>',
            'description_rookie' => "Gift Micro Morgi on chat from {{leader_full_name}}",
        ]);

        \App\Models\TransactionType::query()->where('type', 'gift')->update([
            'description_leader' => 'Recurring monthly gift of {{morgi}} Morgis to <a href="/{{rookie_username}}">{{rookie_full_name}}</a>',
            'description_rookie' => "Recurring monthly gift of {{taxed_morgi}} Morgis from {{leader_full_name}}",
        ]);

        \App\Models\TransactionType::query()->where('type', 'bought_micromorgi')->update([
            'description_leader' => "Purchase of {{micromorgi}} micromorgi",
            'description_rookie' => null,
        ]);

        \App\Models\TransactionType::query()->where('type', 'refund')->update([
            'description_leader' => 'System refund following an error to <a href="/{{rookie_username}}">{{rookie_full_name}} (#{{referal_internal_id}})</a>',
            'description_rookie' => "System refund following an error to {{leader_full_name}} (#{{referal_internal_id}})",
        ]);

        \App\Models\TransactionType::query()->where('type', 'withdrawal')->update([
            'description_leader' => null,
            'description_rookie' => "Withdrawal {{payment_method}} ({{payment_info}})",
        ]);

        \App\Models\TransactionType::query()->where('type', 'withdrawal_rejected')->update([
            'description_leader' => null,
            'description_rookie' => "Withdrawal {{payment_method}} rejected (original payment: #{{referal_internal_id}})",
        ]);

        \App\Models\TransactionType::query()->where('type', 'rookie_block_leader')->update([
            'description_leader' => 'Canceled connection with <a href="/{{rookie_username}}">{{rookie_full_name}}</a> refund',
            'description_rookie' => "Canceled connection with {{leader_full_name}} refund",
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
