<?php

namespace App\Http\Controllers\Admin\Api;

use App\Models\Coupon;
use App\Models\Leader;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class  AdminApiCouponController {

    public function getLeaderCoupons(Leader $leader, Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start' => ['string', 'nullable'],
            'length' => ['string', 'nullable'],
            'draw' => ['nullable'],
            'status' => ['string', 'nullable']
        ]);

        if($validator->fails()){
            return response()->json(['status' => 404, 'message' => $validator->errors()->getMessages()], 404);
        }

        $offset = $request->has('start') ? $request->get('start') : 1;
        $limit = $request->has('length') ? $request->get('length') : 25;

        $query = Coupon::query()
            ->where('leader_id', $leader->id);

        $all = $query->count();

        $query->orderBy('created_at', 'DESC');

        $filtered = $query->count();

        $coupons = $query->offset($offset)
            ->limit($limit)
            ->get();

        $max_pages = ceil($all/$limit);

        $transaction_gift_with_coupon_type_id = TransactionType::query()->where('type', 'gift_with_coupon')->first()->id;
        $transaction_gift_type_id = TransactionType::query()->where('type', 'gift')->first()->id;

        $data_coupons = [];
        foreach ($coupons as $coupon){

            $from_transaction = $coupon->from_transaction;
            if(!isset($from_transaction)){
                continue;
            }

            if(isset($from_transaction->rookie_id)){
                $rookie = User::query()->find($from_transaction->rookie_id);
                if(!isset($rookie)){
                    continue;
                }
            }

            if(isset($from_transaction->admin_id)){
                $admin = User::query()->find($from_transaction->admin_id);
                if(!isset($admin)){
                    continue;
                }
            }

            $first_coupon = '---';
            if($from_transaction->type === 'gift'){
                if(is_null($from_transaction->leader_payment_id)){

                    $used_coupon = Coupon::query()
                        ->where('to_transaction_id', $from_transaction->id)
                        ->first();

                    if(isset($used_coupon)){
                        $first_coupon = $used_coupon->id;
                    }else{
                        $first_coupon = 'COUPON NOT FOUND';
                    }
                }else{

                    $first_coupon = 'YES';
                }
            }

            $data_coupons[] = array(
                'coupon_id' => $coupon->id,
                'date' => $coupon->created_at->format('d/m/Y'),
                'for_transaction_id' => ($from_transaction->type==='bonus') ? 'BONUS' : $coupon->from_transaction_id,
                'rookie_revoked' => (isset($rookie)) ? $rookie->full_name : null,
                'morgi' => $coupon->currency_value,
                'first_coupon' => $first_coupon,
                'given_by' => ($from_transaction->type==='bonus' && isset($admin)) ? $admin->full_name : null
            );
        }

        $data = [];
        $data["draw"] = intval($request->draw);
        $data['pages'] = $max_pages;
        $data['data'] = $data_coupons;
        $data['recordsTotal'] = $all;
        $data['recordsFiltered'] = $filtered;

        return response()->json($data);
    }

}
