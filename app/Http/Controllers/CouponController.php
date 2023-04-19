<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    public function indexTransactions(Request $request): JsonResponse
    {
        $user = $request->user();

        $transactions = Transaction::query()
            ->where('leader_id', $user->id)
            ->whereNotNull('coupon_id')
            ->where(function ($query){
                $query->whereNull('leader_payment_id')
                    ->orWhere('type', '!=',  'gift');
            })
            ->orderByDesc('created_at')
            ->paginate($request->get('limit', 30));

        $response = TransactionResource::compute(
            $request,
            $transactions
        )->get();

        return response()->json($response);
    }
}
