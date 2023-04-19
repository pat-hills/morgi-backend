<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateTransactionsTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::statement("ALTER TABLE transactions_types CHANGE
            COLUMN type type ENUM('chat','gift','withdrawal','refund','bonus','withdrawal_rejected','bought_micromorgi','rookie_block_leader','refund_bonus','chargeback','withdrawal_pending')
            NOT NULL");

        DB::statement("ALTER TABLE transactions CHANGE
            COLUMN type type ENUM('chat','gift','withdrawal','refund','bonus','withdrawal_rejected','bought_micromorgi','withdrawal_pending')
            NOT NULL");

        \App\Models\TransactionType::query()->where('type', 'gift')->update([
            'description_rookie' => "MOnthly Recurring GIft from {{leader_full_name}}"
        ]);

        \App\Models\TransactionType::query()->where('type', 'chat')->update([
            'description_rookie' => "On-chat Micro Morgis gift from {{leader_full_name}}"
        ]);

        \App\Models\TransactionType::query()->where('type', 'refund')->update([
            'description_rookie' => "System refund to {{leader_full_name}} (#{{referal_internal_id}})"
        ]);

        \App\Models\TransactionType::query()->where('type', 'rookie_block_leader')->update([
            'description_rookie' => "Refund to {{leader_full_name}} following your block (#{{referal_internal_id}})"
        ]);

        \App\Models\TransactionType::query()->create([
            'type' => 'refund_bonus', 'description_rookie' => "Morgi system decrease",
            'description_leader' => "Morgi system decrease", 'lang' => 'EN'
        ]);

        \App\Models\TransactionType::query()->create([
            'type' => 'chargeback', 'description_rookie' => "Reduction following a chargeback (#{{referal_internal_id}})",
            'description_leader' => 'System refund following an error to <a href="/rookie-profile/{{rookie_id}}">{{rookie_full_name}} (#{{referal_internal_id}})</a>', 'lang' => 'EN'
        ]);

        \App\Models\TransactionType::query()->create([
            'type' => 'withdrawal_pending', 'lang' => 'EN',
            'description_rookie' => "Pending via {{payment_method}} {{payment_info}}: {{taxed_dollars}} for the payment period of {{payment_period_start_date}} - {{payment_period_end_date}}"
        ]);

        \App\Models\TransactionType::query()->where('type', 'withdrawal_rejected')->update([
            'description_rookie' => "Rejected via {{payment_method}} {{payment_info}} at {{payment_rejected_at}}: {{taxed_dollars}} for the payment period of {{payment_period_start_date}} - {{payment_period_end_date}}"
        ]);

        \App\Models\TransactionType::query()->where('type', 'withdrawal')->update([
            'description_rookie' => "Approved via {{payment_method}} {{payment_info}} at {{payment_approved_at}}: {{taxed_dollars}} for the payment period of {{payment_period_start_date}} - {{payment_period_end_date}}"
        ]);

        \App\Models\TransactionType::query()->where('type', 'bonus')->update([
            'description_rookie' => "Morgi system bonus", 'description_leader' => "Morgi system bonus"
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
