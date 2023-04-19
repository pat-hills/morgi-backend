<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixWithdrawalTransactionsTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        \App\Models\TransactionType::query()->where('type', 'withdrawal_pending')->update([
            'description_rookie' => "Pending via {{payment_method}} {{payment_info}}: {{taxed_dollars}}$ for the payment period of {{payment_period_start_date}} - {{payment_period_end_date}}"
        ]);

        \App\Models\TransactionType::query()->where('type', 'withdrawal_rejected')->update([
            'description_rookie' => "Rejected via {{payment_method}} {{payment_info}} at {{payment_rejected_at}}: {{taxed_dollars}}$ for the payment period of {{payment_period_start_date}} - {{payment_period_end_date}}"
        ]);

        \App\Models\TransactionType::query()->where('type', 'withdrawal')->update([
            'description_rookie' => "Approved via {{payment_method}} {{payment_info}} at {{payment_approved_at}}: {{taxed_dollars}}$ for the payment period of {{payment_period_start_date}} - {{payment_period_end_date}}"
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
