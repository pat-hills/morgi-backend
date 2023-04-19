<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsGoalTransactionRefundToTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->boolean('is_goal_transaction_refund')->default(false)->after('goal_id');
        });

        $transactions = \App\Models\Transaction::query()
            ->where('type', 'refund')
            ->whereNotNull('goal_id')
            ->get();
        foreach ($transactions as $transaction){
            $refunded_transaction = \App\Models\Transaction::query()
                ->where('type', 'goal')
                ->where('internal_id', $transaction->referal_internal_id)
                ->first();
            if(isset($refunded_transaction)){
                $transaction->update([
                    'is_goal_transaction_refund' => true
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('is_goal_transaction_refund');
        });
    }
}
