<?php

namespace App\Http\Controllers\Admin\RefactorWorkaround;

use App\Enums\UserEnum;
use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Models\Transaction;
use App\Models\TransactionRefund;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;


class RefactorUserMicromorgiController extends Controller
{
    public function getUserMicromorgi(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start' => ['sometimes', 'string', 'nullable'],
            'length' => ['sometimes', 'string', 'nullable'],
            'draw' => ['sometimes', 'nullable'],
            'search' => ['sometimes', 'nullable', 'array']
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->getMessages()], Response::HTTP_BAD_REQUEST);
        }

        $offset = $request->has('start') ? $request->get('start') : 1;
        $limit = $request->has('length') ? $request->get('length') : 25;
        $search = $request->has('search') ? $request->search['value'] : null;

        $user_column_name = null;

        switch ($user->type) {
            case UserEnum::TYPE_LEADER:
                $user_column_name = 'leader_id';
                break;
            case UserEnum::TYPE_ROOKIE:
                $user_column_name = 'rookie_id';
                break;
            default:
                return response()->json(['message' => 'User type not valid!'], Response::HTTP_BAD_REQUEST);
        }

        $transaction_query = Transaction::query()
            ->where($user_column_name, $user->id)
            ->whereIn('transactions.type', ['chat', 'bonus', 'bought_micromorgi', 'refund', 'fine', 'goal', 'goal_refund'])
            ->whereNull('transactions.morgi');

        $all = $transaction_query->count();

        if($search){
            $transaction_query->where(function ($query) use ($search){
                $query->where('internal_id', 'LIKE', "%{$search}%")
                    ->orWhere('referal_internal_id', 'LIKE', "%{$search}%");
            });
        }

        $filtered = $transaction_query->count();

        $transaction_query->offset($offset)
            ->limit($limit)
            ->orderByDesc('created_at');

        $transactions = $transaction_query->get()->append([]);

        $max_pages = ceil($all / $limit);


        $admin_ids = collect();
        $admin_ids = $admin_ids->merge($transactions->pluck('admin_id'));
        $admin_ids = $admin_ids->merge($transactions->pluck('refunded_by'));

        $leader_ids = $admin_ids->merge($transactions->pluck('leader_id'));
        $rookie_ids = $admin_ids->merge($transactions->pluck('rookie_id'));

        $admins = User::find($admin_ids->toArray());
        $leaders = User::find($leader_ids->toArray());
        $rookies = User::find($rookie_ids->toArray());

        $transactions_refunded = Transaction::query()
            ->whereIn('internal_id', $transactions->pluck('referal_internal_id'))
            ->get();

        $transactions_refund_history = TransactionRefund::query()
            ->whereIn('transaction_id', $transactions->pluck('id'))
            ->get();

        $goals = Goal::query()
            ->whereIn('id', $transactions->pluck('goal_id'))
            ->get();

        $transactions->map(function ($transaction) use ($admins, $leaders, $rookies, $transactions_refunded, $transactions_refund_history, $goals) {

            $transaction->refunded_by_admin = null;
            if (isset($transaction->refunded_by)) {
                $admin = $admins->where('id', $transaction->refunded_by)->first();
                $transaction->refunded_by_admin = $admin ?? null;
            }

            $transaction->admin = null;
            if (isset($transaction->admin_id)) {
                $admin = $admins->where('id', $transaction->admin_id)->first();
                $transaction->admin = $admin ?? null;
            }

            $transaction->leader = null;
            if (isset($transaction->leader_id)) {
                $leader = $leaders->where('id', $transaction->leader_id)->first();
                $transaction->leader = $leader ?? null;
            }

            $transaction->rookie = null;
            if (isset($transaction->rookie_id)) {
                $rookie = $rookies->where('id', $transaction->rookie_id)->first();
                $transaction->rookie = $rookie ?? null;
            }

            $transaction->transaction_refunded = null;
            if (isset($transaction->referal_internal_id)) {
                $transaction_refunded = $transactions_refunded->where('internal_id', $transaction->referal_internal_id)->first();
                $transaction->transaction_refunded = $transaction_refunded ?? null;
            }

            $transaction_refund_history = $transactions_refund_history->where('id', $transaction->id)->last();
            $transaction->refund_history = (isset($transaction_refund_history)) ? $transaction_refund_history : null;

            $transaction->datetime = date('Y-m-d H:i:s', strtotime($transaction->created_at));
            $transaction->refunded_datetime = (isset($transaction->refunded_at)) ? date('Y-m-d H:i:s', strtotime($transaction->refunded_at)) : null;

            $goal = $goals->where('id', $transaction->goal_id)->first();
            $transaction->goal = $goal ?? null;

            return $transaction;
        });

        $data = [
            'draw' => intval($request->draw),
            'pages' => $max_pages,
            'data' => $transactions,
            'recordsTotal' => $all,
            'recordsFiltered' => $filtered
        ];

        return response()->json($data);
    }
}
