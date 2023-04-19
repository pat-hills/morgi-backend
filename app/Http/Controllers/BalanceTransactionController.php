<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Leader;
use App\Models\Rookie;
use App\Models\Transaction;
use Illuminate\Http\Request;

class BalanceTransactionController extends Controller
{
    public function rookieMorgiTransactions(Request $request)
    {
        $user = $request->user();

        $transactions = Transaction::query()->select('transactions.*')
            ->join('users', 'users.id', '=', 'transactions.leader_id')
            ->where('transactions.rookie_id', $user->id)
            ->where('users.active', true)
            ->whereIn('transactions.type', ['gift', 'refund', 'bonus'])
            ->whereNotNull('morgi')
            ->orderByDesc('transactions.created_at')
            ->paginate($request->get('limit', 30));

        $response = TransactionResource::compute(
            $request,
            $transactions
        )->get();

        return response()->json($response);
    }

    public function rookieMorgiTransactionsFromLeader(Request $request, Leader $leader)
    {
        $user = $request->user();

        $transactions = Transaction::query()
            ->where('leader_id', $leader->id)
            ->where('rookie_id', $user->id)
            ->whereIn('type', ['gift', 'refund'])
            ->whereNotNull('morgi')
            ->orderByDesc('created_at')
            ->paginate($request->get('limit', 15));

        $response = TransactionResource::compute(
            $request,
            $transactions
        )->get();

        $response->put('micro_morgi_given', $leader->getMicroMorgiGivenToRookie($user->id));
        $response->put('morgi_given', $leader->getMorgiGivenToRookie($user->id));

        return response()->json($response);
    }

    public function rookieMicroMorgiTransactionsFromLeader(Request $request, Leader $leader)
    {
        $user = $request->user();

        $transactions = Transaction::query()
            ->where('leader_id', $leader->id)
            ->where('rookie_id', $user->id)
            ->whereIn('type', ['chat', 'refund', 'bonus'])
            ->whereNotNull('micromorgi')
            ->orderByDesc('created_at')
            ->paginate($request->get('limit', 30));

        $response = TransactionResource::compute(
            $request,
            $transactions
        )->get();

        return response()->json($response);
    }

    public function leaderMicroMorgiTransactions(Request $request)
    {
        $user = $request->user();

        $new_transactions = Transaction::select('transactions.*')
            ->leftJoin('users', 'users.id', '=', 'transactions.rookie_id')
            ->where('users.active', true)
            ->where('transactions.leader_id', $user->id)
            ->whereIn('transactions.type', ['chat', 'goal'])
            ->whereNotNull('micromorgi');

        $refund_transactions = Transaction::where('leader_id', $user->id)
            ->where('type', 'refund')
            ->whereNotNull('micromorgi')
            ->whereNull('leader_payment_id');

        $bonus_transactions = Transaction::where('leader_id', $user->id)
            ->where('type', 'bonus')
            ->whereNotNull('micromorgi');

        $transactions = $new_transactions
            ->union($refund_transactions)
            ->union($bonus_transactions)
            ->orderByDesc('created_at')
            ->paginate($request->get('limit', 30));

        $response = TransactionResource::compute(
            $request,
            $transactions
        )->get();

        return response()->json($response);
    }

    public function morgiGivenToRookie(Request $request, Rookie $rookie)
    {
        $user = $request->user();

        $count = Transaction::query()->select('morgi')
            ->where('leader_id', $user->id)
            ->where('rookie_id', $rookie->id)
            ->where('type', 'gift')
            ->whereNotNull('morgi')
            ->whereNull('refund_type')
            ->sum('morgi');

        return response()->json($count);
    }

    public function microMorgiGivenToRookie(Request $request, Rookie $rookie)
    {
        $user = $request->user();

        $count = Transaction::query()->select('micromorgi')
            ->where('leader_id', $user->id)
            ->where('rookie_id', $rookie->id)
            ->where('type', 'chat')
            ->whereNotNull('micromorgi')
            ->whereNull('refund_type')
            ->sum('micromorgi');

        return response()->json($count);
    }
}
