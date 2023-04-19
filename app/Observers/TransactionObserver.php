<?php

namespace App\Observers;

use App\Models\Leader;
use App\Models\Transaction;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return void
     */
    public function created(Transaction $transaction)
    {
        if(is_null($transaction->leader_id)){
            return;
        }

        $leader = Leader::find($transaction->leader_id);
        if($leader->has_approved_transaction){
            $transaction->update(['internal_status' => 'approved']);
        }
    }

    /**
     * Handle the Transaction "updated" event.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return void
     */
    public function updated(Transaction $transaction)
    {
        if(is_null($transaction->leader_id)){
           return;
        }

        $leader = Leader::find($transaction->leader_id);
        if($leader->has_approved_transaction){
            return;
        }

        $counter_approved = Transaction::where('leader_id', $leader->id)
            ->where('internal_status', 'approved')
            ->get()
            ->count();

        if($counter_approved >= 3){
            $leader->update(['has_approved_transaction' => 1]);
            Transaction::where('leader_id', $leader->id)->where('internal_status', 'pending')->update(['internal_status' => 'approved']);
        }
    }
}
