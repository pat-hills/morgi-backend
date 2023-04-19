<?php

namespace App\Http\Controllers\Admin\Api;

use App\Ccbill\CcbillCurrencyCodes;
use App\Http\Controllers\Controller;
use App\Models\LeaderPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminApiTransactionController extends Controller
{

    public function getTransactionsByUser(User $user, Request $request)
    {

        $validator = Validator::make($request->all(), [
            'start' => ['string', 'nullable'],
            'length' => ['string', 'nullable'],
            'draw' => ['nullable'],
            'status' => ['string', 'nullable']
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 404, 'message' => $validator->errors()->getMessages()], 404);
        }

        $transaction_select = [
            'transactions.id',
            'transactions.morgi',
            'transactions.micromorgi',
            'transactions.dollars',
            'transactions.subscription_id',
            'transactions.internal_id',
            'transactions.created_at',
            'transactions.type',
            'transactions.refund_type',
            'transactions.admin_id',
            'transactions.refunded_at',
            'transactions.refunded_by',
            'transactions.rookie_id',
            'transactions.admin_id',
            'transactions.referal_internal_id',
        ];

        $leaders_ccbill_data_select = [
            'leaders_ccbill_data.cardType',
            'leaders_ccbill_data.billedCurrencyCode'
        ];

        $leaders_payments_select = [
            'leaders_payments.ip_address',
            'leaders_payments.ccbill_subscriptionId',
            'leaders_payments.ccbill_transactionId',
        ];

        $transactions_refunds_select = [
            'transactions_refunds.status as refund_status',
            'transactions_refunds.error as refund_error',
            'transactions_refunds.admin_id as refunded_by',
        ];

        $query = LeaderPayment::query()
            ->addSelect($transaction_select)
            ->addSelect($leaders_ccbill_data_select)
            ->addSelect($leaders_payments_select)
            ->addSelect($transactions_refunds_select);

        $query->where('leaders_payments.leader_id', $user->id)
            ->join('transactions', 'leaders_payments.id', '=', 'transactions.leader_payment_id')
            ->join('leaders_ccbill_data', 'leaders_payments.leader_payment_method_id', '=', 'leaders_ccbill_data.id')
            ->leftJoin('transactions_refunds', function($join) {
                $join->on('transactions_refunds.transaction_id', '=', 'transactions.id')
                    ->on('transactions_refunds.id', '=', DB::raw("(SELECT max(id) from transactions_refunds WHERE transactions_refunds.transaction_id = transactions.id)"));
            });

        $all = $query->count();

        $offset = $request->has('start') ? $request->get('start') : 0;
        $limit = $request->has('length') ? $request->get('length') : 25;

        $filtered = $query->count();

        $query->orderBy('leaders_payments.created_at', 'DESC');

        $query->offset($offset)
            ->limit($limit);

        $transactions = $query->get();

        $max_pages = ceil($filtered / $limit);

        $rookies = User::query()
            ->whereIn('id', $transactions->pluck('rookie_id'))
            ->get()
            ->keyBy('id');

        $fields = [];

        $administrators = User::query()
            ->whereIn('id', $transactions->pluck('refunded_by'))
            ->get();

        foreach ($transactions as $transaction) {
            $field = [];
            $field = array_merge($field, $transaction->getOriginal());
            $field['biller'] = 'CCBILL';
            $field['leader'] = $user;
            $field['rookie'] = $rookies[$transaction->rookie_id] ?? null;
            $field['billedCurrencyCodeLabel'] = CcbillCurrencyCodes::CURRENCY[$transaction->billedCurrencyCode] ?? '';
            $field['is_recurring'] = ($transaction->type === 'gift') ? 'YES' : null;

            $field['refunded_by_username'] = null;
            $admin = $administrators->where('id', $transaction->refunded_by)->first();
            if(isset($admin)){
                $field['refunded_by_username']  = $admin->username;
            }

            $fields[] = $field;
        }

        $data = [];
        $data['draw'] = intval($request->draw);
        $data['pages'] = $max_pages;
        $data['data'] = $fields;
        $data['recordsTotal'] = $all;
        $data['recordsFiltered'] = $filtered;

        return response()->json($data);
    }
}
