<?php

namespace App\Http\Controllers\Admin\Api;

use App\Ccbill\CcbillCurrencyCodes;
use App\Enums\TransactionRefundEnum;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionRefund;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AdminApiLeaderPaymentController extends Controller
{

    public function getTransactionRefunds(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start' => ['sometimes', 'string', 'nullable'],
            'length' => ['sometimes', 'string', 'nullable'],
            'draw' => ['sometimes', 'nullable'],
            'status' => ['sometimes', 'nullable', Rule::in(TransactionRefundEnum::STATUSES)],
            'leaders_ids' => ['sometimes', 'nullable', 'array'],
            'leaders_ids.*' => ['sometimes', 'integer', 'exists:leaders,id'],
            'search' => ['sometimes', 'nullable', 'array']
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 404,
                'message' => $validator->errors()->getMessages()
            ], 404);
        }

        $select = [
            'transactions.id',
            'transactions.type',
            'transactions.rookie_id',
            'transactions.leader_id',
            'transactions.morgi',
            'transactions.micromorgi',
            'transactions.dollars',
            'transactions.refunded_at',
            'transactions.refund_type',
            'transactions.internal_id',
            'transactions.referal_internal_id',
            'leaders_payments.ccbill_subscriptionId',
            'leaders_payments.ccbill_transactionId',
            'leaders_ccbill_data.email',
            'leaders_ccbill_data.ipAddress',
            'leaders_payments.created_at',
            'leaders_ccbill_data.billedCurrencyCode',
            'leaders_ccbill_data.cardType',
            'transactions_refunds.status',
            'transactions_refunds.error',
            'transactions_refunds.status',
            'transactions_refunds.admin_id'
        ];

        $query = Transaction::query()
            ->select($select)
            ->join('leaders_payments', 'leaders_payments.id', '=', 'transactions.leader_payment_id')
            ->join('leaders_ccbill_data', 'leaders_ccbill_data.id', '=', 'leaders_payments.leader_payment_method_id')
            ->join('transactions_refunds', function($join) {
                $join->on('transactions_refunds.transaction_id', '=', 'transactions.id')
                    ->on('transactions_refunds.id', '=', DB::raw("(select max(id) from transactions_refunds WHERE transactions_refunds.transaction_id = transactions.id)"));
            });

        if($request->has('status')){
            $query->where('transactions_refunds.status', $request->status);
        }

        $all = $query->count();

        if($request->has('leaders_ids') && !empty($request->leaders_ids)){
            $query->whereIn('transactions.leader_id', $request->leaders_ids);
        }

        if($request->has('search') && is_array($request->search) && !empty($request->search['value'])){
            $search_value = $request->search['value'];
            $query->where(function($query) use ($search_value){
                    $query->where('transactions.internal_id', 'like', "$search_value%")
                        ->orWhere('leaders_payments.ccbill_subscriptionId', 'like', "$search_value%")
                        ->orWhere('leaders_payments.ccbill_transactionId', 'like', "$search_value%");
            });
        }

        $offset = $request->has('start') ? $request->get('start') : 0;
        $limit = $request->has('length') ? $request->get('length') : 25;

        $filtered = $query->count();

        $query->orderBy('transactions_refunds.updated_at', 'DESC');

        $query->offset($offset)
            ->limit($limit);

        $transactions = $query->get();

        $max_pages = ceil($filtered/$limit);

        $admins_ids = $transactions->pluck('admin_id');

        $administrators = User::query()
            ->whereIn('id', $admins_ids)
            ->get()
            ->keyBy('id');

        $rookies = User::query()
            ->whereIn('id', $transactions->pluck('rookie_id'))
            ->get()
            ->keyBy('id');

        $leaders = User::query()
            ->whereIn('id', $transactions->pluck('leader_id'))
            ->get()
            ->keyBy('id');

        $transactions = $transactions->toArray();

        foreach ($transactions as &$transaction){
            $transaction['biller'] = 'CCBILL';
            $transaction['billedCurrencyCodeLabel'] = CcbillCurrencyCodes::CURRENCY[$transaction['billedCurrencyCode']] ?? null;
            $transaction['admin'] = $administrators[$transaction['admin_id']] ?? null;
            $transaction['is_recurring'] = ($transaction['type'] === 'gift') ? 'YES' : 'NO';
            $transaction['leader'] = $leaders[$transaction['leader_id']] ?? null;
            $transaction['rookie'] = $rookies[$transaction['rookie_id']] ?? null;
        }

        $data = [];
        $data['draw'] = intval($request->draw);
        $data['pages'] = $max_pages;
        $data['data'] = $transactions;
        $data['recordsTotal'] = $all;
        $data['recordsFiltered'] = $filtered;

        return response()->json($data);
    }
}
