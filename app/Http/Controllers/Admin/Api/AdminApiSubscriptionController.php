<?php

namespace App\Http\Controllers\Admin\Api;

use App\Enums\LeaderPaymentEnum;
use App\Models\Coupon;
use App\Models\LeaderPayment;
use App\Models\Subscription;
use App\Models\SubscriptionPackage;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;

class  AdminApiSubscriptionController {

    public function getSubscriptionPackages(): JsonResponse
    {
        return response()->json(SubscriptionPackage::all());
    }

    public function getSubscriptionHistory(Subscription $subscription): JsonResponse
    {
        $transactions_collection = Transaction::query()
            ->where('subscription_id', $subscription->id)
            ->orderBy('created_at', 'DESC')
            ->get();

        $transactions = [];

        $leaders_payments_ids = $transactions_collection->pluck('leader_payment_id');
        $leaders_payments_collection = LeaderPayment::query()
            ->whereIn('id', $leaders_payments_ids)
            ->where('subscription_id', $subscription->id)
            ->get();
        foreach ($leaders_payments_collection as $leader_payment){
            $transactions['data'][] = [
                'id' => $leader_payment->id,
                'type' => 'payment',
                'created_at' => $leader_payment->created_at->format('y-M-d, h:i A'),
                'created_at_timestamp' => $leader_payment->created_at->timestamp,
                'status' => $leader_payment->status,
                'amount' => $leader_payment->amount
            ];
        }

        $coupons_ids = $transactions_collection->pluck('coupon_id');
        $transactions_coupons_collection = Transaction::query()
            ->whereIn('coupon_id', $coupons_ids)
            ->where('subscription_id', $subscription->id)
            //to create constants on Transaction model
            ->whereIn('type', ['gift'])
            ->get();
        foreach ($transactions_coupons_collection as $transaction_coupon) {
            $transactions['data'][] = [
                'id' => $transaction_coupon->id,
                'type' => 'coupon',
                'created_at' => $transaction_coupon->created_at->format('y-M-d, h:i A'),
                'created_at_timestamp' => $transaction_coupon->created_at->timestamp,
                'status' => 'coupon',
                'amount' => $transaction_coupon->morgi
            ];
        }

        $collection = collect($transactions['data']);
        $collection = $collection->sortByDesc('created_at_timestamp')->values();

        $total = 0;
        foreach ($transactions['data'] as $transaction){
            if (in_array($transaction['status'], [LeaderPaymentEnum::STATUS_PAID, 'coupon'])){
                $total += $transaction['amount'];
            }
        }

        $transactions['data'] = $collection->toArray();
        $transactions['total'] = $total;

        return response()->json($transactions);
    }

}
