<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaderPayment;
use App\Models\Transaction;
use App\Models\User;

class AdminApiUserController extends Controller
{

    public function getUserInfo(User $user){

        $user->tot_spend_usd = LeaderPayment::query()
            ->where('leader_id', $user->id)
            ->where('status', 'paid')
            ->sum('dollar_amount');

        $user->chargeback_usd = Transaction::query()
            ->where('leader_id', $user->id)
            ->where('transactions.refund_type', 'chargeback')
            ->sum('dollars');

        return response()->json($user);
    }
}
