<?php


namespace App\Utils;


use App\Models\Coupon;
use App\Models\Leader;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Transactions\Morgi\TransactionLeaderBonusCoupon;

class CouponUtils
{
    private $leader;
    private $from_transaction = null;

    public function __construct(int $leader_id)
    {
        try {
            $leader = Leader::query()->findOrFail($leader_id);
            $this->leader = $leader;
        }catch (\Exception $exception){
            throw new \Exception("Unable to retrieve leader");
        }
    }

    public function giveBonusCoupon(float $currency_value, int $admin_id, string $notes, string $admin_description = null): Coupon
    {
        try {
            $this->from_transaction = TransactionLeaderBonusCoupon::create(
                $this->leader->id,
                $currency_value,
                $admin_id,
                $notes,
                $admin_description
            );
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        $coupon = $this->createCoupon($currency_value);
        $this->from_transaction->update(['coupon_id' => $coupon->id]);

        NotificationUtils::sendNotification($this->leader->id, 'got_bonus_coupon', now(), [
            'amount_morgi' => $currency_value
        ]);

        return $coupon;
    }

    public function giveRefundedTransactionCoupon(int $transaction_id): Coupon
    {
        $transaction = Transaction::query()->find($transaction_id);
        if(!isset($transaction) || $transaction->leader_id!==$this->leader->id){
            throw new \Exception("The transaction provided is not valid");
        }

        $this->from_transaction = $transaction;
        $currency_value = $this->from_transaction->morgi;

        $coupon = $this->createCoupon($currency_value);

        $transaction_type = TransactionType::query()->where('type', 'not_refund_gift_with_coupon')->first();
        if(!isset($transaction_type)){
            $transaction_type = TransactionType::query()->where('type', 'gift')->first();
        }

        if(!isset($transaction->coupon_id)){
            $transaction->update([
                'coupon_id' => $coupon->id,
                'transaction_type_id' => $transaction_type->id
            ]);
        }

        NotificationUtils::sendNotification($this->leader->id, 'got_refunded_gift_coupon', now(), [
            'amount_morgi' => $currency_value,
            'ref_user_id' => $this->from_transaction->rookie_id
        ]);

        NotificationUtils::sendNotification($this->from_transaction->rookie_id, 'gift_refunded_inactivity', now(), [
            'ref_user_id' => $this->leader->id
        ]);

        return $coupon;
    }

    private function createCoupon(float $currency_value): Coupon
    {
        $this->leader->increment('total_coupons_got');

        return Coupon::create([
            'from_transaction_id' => $this->from_transaction->id,
            'leader_id' => $this->leader->id,
            'currency_value' => $currency_value
        ]);
    }
}
