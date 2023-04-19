<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Goal;
use App\Models\PaymentPeriod;
use App\Models\Rookie;
use App\Models\Subscription;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if($user->type === 'leader') {
            $validator = Validator::make($request->all(), [
                'month' => ['sometimes', 'date']
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $transactions = Transaction::query()
                ->where('leader_id', $user->id)
                ->whereNotNull('leader_payment_id');

            if($request->has('month')) {
                $year = Carbon::create($request->month)->format('Y');
                $month = Carbon::create($request->month)->format('m');
                $transactions = $transactions->whereYear('created_at', $year)->whereMonth('created_at', $month);
            }

           $transactions = $transactions
               ->orderByDesc('created_at')
               ->where(function ($query){
                   $query->whereNull('coupon_id')
                       ->orWhere('type', '!=', 'refund');
               })
               ->paginate($request->query('limit', 30));

            $response = TransactionResource::compute(
                $request,
                $transactions
            )->get();

            $dollar_sum = $transactions->where('type', '!=', 'refund')
                ->whereNull('refund_type')
                ->whereNotNull('leader_payment_id')
                ->sum('dollars');

            $recurring_payments = Subscription::query()
                ->where('leader_id', $user->id)
                ->where('status', 'active')
                ->whereNull('deleted_at')
                ->count();

            $first_transaction_at = Transaction::query()
                ->where('leader_id', $user->id)
                ->first();

            $response->put('dollar_sum', $dollar_sum);
            $response->put('recurring_payments', $recurring_payments);
            $response->put('first_transaction_at', $first_transaction_at->created_at ?? null);

            return response()->json($response);
        }

        if($user->type === 'rookie') {
            $validator = Validator::make($request->all(), [
                'start_at' => ['sometimes', 'date_format:Y-m-d'],
                'end_at' => ['sometimes', 'date_format:Y-m-d', 'after_or_equal:start_at']
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $rookie = Rookie::find($user->id);

            $start_at = $this->computeStartAt($request->start_at);
            $end_at = $this->computeEndAt($request->end_at);

            $transactions = Transaction::query()->where('rookie_id', $user->id)
                ->where('created_at', '>=', $start_at)
                ->where('created_at', '<=', $end_at)
                ->where('type', '!=', 'goal')
                ->where('is_goal_transaction_refund', false);

            $transactions_response = $transactions->orderByDesc('created_at');

            if (isset($request->show_payments) && $request->show_payments){
                $transactions_response = $transactions_response->whereIn('type', ['withdrawal', 'withdrawal_rejected', 'withdrawal_pending']);
            }

            $transactions_response = TransactionResource::compute(
                $request,
                $transactions_response->paginate($request->query('limit', 30))
            )->get();

            $transactions_for_balance = Transaction::query()->where('rookie_id', $user->id)
                ->where('created_at', '>=', $start_at)
                ->where('created_at', '<=', $end_at)
                ->where('type', '!=', 'goal')
                ->where('is_goal_transaction_refund', false)
                ->get();

            if (isset($request->show_payments) && $request->show_payments){
                $payments_received = $transactions_for_balance
                    ->where('type', 'withdrawal')
                    ->sum('taxed_dollars');

                $transactions_received_this_period = Transaction::query()->select('transactions.*')
                    ->leftJoin('payments_rookies', 'payments_rookies.id', '=', 'transactions.payment_rookie_id')
                    ->where('transactions.rookie_id', $user->id)
                    ->where('transactions.type', 'withdrawal')
                    ->whereNotIn('transactions.id', $transactions_for_balance->where('type', 'withdrawal')->pluck('id')->toArray())
                    ->whereNotNull('payments_rookies.approved_at')
                    ->whereNull('transactions.refund_type')
                    ->where('payments_rookies.approved_at', '>=', $start_at)
                    ->sum('transactions.taxed_dollars');

                $transactions_pending = Transaction::query()
                    ->where('rookie_id', $user->id)
                    ->where('type', 'withdrawal_pending')
                    ->sum('taxed_dollars');

                $payments_pending = $rookie->withdrawal_balance + $transactions_pending;
                $payments_received = $payments_received + $transactions_received_this_period;
            } else {
                $morgi_balance = $transactions_for_balance
                    ->whereNotIn('type', ['withdrawal', 'withdrawal_rejected', 'withdrawal_pending', 'fine', 'refund'])
                    ->sum('morgi');

                $micromorgi_balance = $transactions_for_balance
                    ->whereNotIn('type', ['withdrawal', 'withdrawal_rejected', 'withdrawal_pending', 'fine', 'refund'])
                    ->sum('micromorgi');

                $morgi_balance_to_remove = $transactions_for_balance
                    ->whereIn('type', ['fine', 'refund'])
                    ->sum('morgi');

                $micromorgi_balance_to_remove = $transactions_for_balance
                    ->whereIn('type', ['fine', 'refund'])
                    ->sum('micromorgi');

                $morgi_balance = ($morgi_balance <= 0 || ($morgi_balance - $morgi_balance_to_remove) <= 0)
                    ? 0
                    : $morgi_balance - $morgi_balance_to_remove;

                $micromorgi_balance = ($micromorgi_balance <= 0 || ($micromorgi_balance - $micromorgi_balance_to_remove) <= 0)
                    ? 0
                    : $micromorgi_balance - $micromorgi_balance_to_remove;
            }

            $start_at_date = Carbon::create($start_at)->toDateString();
            $end_at_date = Carbon::create($end_at)->toDateString();

            $transactions_response->put('payments_received', $payments_received ?? 0);
            $transactions_response->put('payments_pending', (isset($payments_pending) && $payments_pending > 0) ? $payments_pending : 0);

            $transactions_response->put('morgi_balance', $morgi_balance ?? 0);
            $transactions_response->put('micromorgi_balance', $micromorgi_balance ?? 0);

            $transactions_response->put('start_at', $start_at_date);
            $transactions_response->put('end_at', $end_at_date);

            return response()->json($transactions_response);
        }

        return response()->json(['message' => 'Error during loading of data'], 400);
    }

    public function goalTransactions(Request $request, Goal $goal)
    {
        $user = $request->user();
        if($user->type !== 'rookie'){
            return response()->json(['message' => 'You are not a rookie'], 400);
        }

        $transactions = Transaction::query()->where('rookie_id', $user->id)
            ->where('goal_id', $goal->id)
            ->whereIn('type', ['goal', 'refund'])
            ->orderBy('type')
            ->orderByDesc('created_at')
            ->paginate($request->query('limit', 30));

        $response = TransactionResource::compute(
            $request,
            $transactions
        )->get();

        $transactions_stats = Transaction::query()->where('rookie_id', $user->id)
            ->where('goal_id', $goal->id)
            ->whereNotNull('leader_id')
            ->where('type', 'goal')
            ->get();

        $transaction_sum = $transactions_stats->sum('micromorgi');

        $leader_count = $transactions_stats->unique('leader_id')->count();

        $response->put('total_donations', $transaction_sum);
        $response->put('total_leaders', $leader_count);

        return response()->json($response);
    }

    private function computeStartAt($start_at = null)
    {
        if(isset($start_at)){
            return Carbon::create($start_at)
                ->setHours(00)
                ->setMinutes(00)
                ->setSeconds(00)
                ->toDateTimeString();
        }

        $payment_period_timestamp = PaymentPeriod::getLastDateTimestamp();

        if(isset($payment_period_timestamp)){
            return $payment_period_timestamp->toDateTimeString();
        }

        return Carbon::now()->subMonth()->toDateTimeString();
    }

    private function computeEndAt($end_at = null)
    {
        if(isset($end_at)){
            return Carbon::create($end_at)
                ->setHours(23)
                ->setMinutes(59)
                ->setSeconds(59)
                ->toDateTimeString();
        }

        return Carbon::now()->toDateTimeString();
    }
}
