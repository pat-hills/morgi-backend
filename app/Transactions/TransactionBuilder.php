<?php

namespace App\Transactions;

use App\Models\Coupon;
use App\Models\Goal;
use App\Models\Leader;
use App\Models\LeaderPayment;
use App\Models\PaymentRookie;
use App\Models\Rookie;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\User;
use App\Models\UserBlock;
use App\Utils\NotificationUtils;
use Carbon\Carbon;

class TransactionBuilder
{
    public $type;

    public $rookie_id = null;
    public $rookie = null;
    public $rookie_user = null;

    public $leader_id = null;
    public $leader = null;
    public $leader_user = null;

    public $admin_id = null;

    public $internal_id;
    public $referal_internal_id = null;

    public $taxed_dollars = null;
    public $dollars = null;

    public $taxed_micromorgi = null;
    public $micromorgi = null;

    public $taxed_morgi = null;
    public $morgi = null;

    public $transaction_type_id;
    public $subscription_id = null;
    public $coupon_id = null;
    public $payment_rookie_id = null;
    public $leader_payment_id = null;
    public $goal_id = null;
    public $is_goal_transaction_refund = false;

    public $internal_status = 'pending';
    public $internal_status_reason = null;
    public $internal_status_by = null;

    public $refund_type = null;
    public $refunded_by = null;
    public $refunded_at = null;

    public $user_block_id = null;
    public $notes = null;
    public $admin_description = null;

    public $has_pulse = false;

    public $transaction = null;
    public $activity_log = null;

    public function __construct(string $transaction_type_name = null)
    {
        if(!isset($this->type)){
            throw new \Exception("Transaction's type is not set");
        }

        if(!isset($transaction_type_name)){
            $transaction_type_name = $this->type;
        }

        $transaction_type = TransactionType::query()->where('type', $transaction_type_name)->first();
        if(!isset($transaction_type)){
            throw new \Exception("Unable to retrieve transaction's type");
        }

        $this->transaction_type_id = $transaction_type->id;
        $this->internal_id = $this->generateInternalId();
    }

    public function setDollars(float $dollars): TransactionBuilder
    {
        $this->dollars = $dollars;
        $this->taxed_dollars = $this->tax($dollars);
        return $this;
    }

    public function setMorgi(float $morgi): TransactionBuilder
    {
        $this->morgi = $morgi;
        $this->taxed_morgi = $this->tax($morgi);
        return $this;
    }

    public function setMicromorgi(float $micromorgi): TransactionBuilder
    {
        $this->micromorgi = $micromorgi;
        $this->taxed_micromorgi = $this->tax($micromorgi);
        return $this;
    }

    public function overrideTaxedDollars(float $dollars): TransactionBuilder
    {
        $this->taxed_dollars = $dollars;
        return $this;
    }

    public function overrideTaxedMorgi(float $morgi): TransactionBuilder
    {
        $this->taxed_morgi = $morgi;
        return $this;
    }

    public function overrideTaxedMicromorgi(float $micromorgi): TransactionBuilder
    {
        $this->taxed_micromorgi = $micromorgi;
        return $this;
    }

    public function setRookieId(int $rookie_id): TransactionBuilder
    {
        $rookie = Rookie::find($rookie_id);
        $rookie_user = User::find($rookie_id);
        if(!isset($rookie_user) || !isset($rookie)){
            throw new \Exception("Unable to retrieve rookie");
        }

        $this->rookie_id = $rookie_id;
        $this->rookie = $rookie;
        $this->rookie_user = $rookie_user;

        return $this;
    }

    public function setLeaderId(int $leader_id): TransactionBuilder
    {
        $leader = Leader::find($leader_id);
        $leader_user = User::find($leader_id);
        if(!isset($leader_user) || !isset($leader)){
            throw new \Exception("Unable to retrieve leader");
        }

        $this->leader_id = $leader_id;
        $this->leader = $leader;
        $this->leader_user = $leader_user;

        return $this;
    }

    public function setSubscriptionId(int $subcription_id): TransactionBuilder
    {
        $subscription = Subscription::find($subcription_id);
        if(!isset($subscription)){
            throw new \Exception("Unable to retrieve subscription");
        }

        $this->subscription_id = $subcription_id;

        return $this;
    }

    public function setCouponId(int $coupon_id): TransactionBuilder
    {
        $coupon = Coupon::find($coupon_id);
        if(!isset($coupon)){
            throw new \Exception("Unable to retrieve coupon");
        }

        $this->coupon_id = $coupon_id;
        return $this;
    }

    public function setGoalId(int $goal_id): TransactionBuilder
    {
        $goal = Goal::find($goal_id);
        if(!isset($goal)){
            throw new \Exception("Unable to retrieve goal");
        }

        $this->goal_id = $goal_id;
        return $this;
    }

    public function setLeaderPaymentId(int $leader_payment_id): TransactionBuilder
    {
        $leader_payment = LeaderPayment::query()->find($leader_payment_id);
        if(!isset($leader_payment) || $leader_payment->leader_id !== $this->leader_id){
            throw new \Exception("Unable to retrieve leader payment");
        }

        $this->leader_payment_id = $leader_payment_id;

        return $this;
    }

    public function setPaymentRookieId(int $payment_rookie_id): TransactionBuilder
    {
        $payment_rookie = PaymentRookie::query()->find($payment_rookie_id);
        if(!isset($payment_rookie)){
            throw new \Exception("Unable to retrieve payment rookie id");
        }

        $this->payment_rookie_id = $payment_rookie_id;

        return $this;
    }

    public function setAdminId(int $admin_id): TransactionBuilder
    {
        $admin = User::query()->find($admin_id);
        if(!isset($admin) || $admin->type !== 'admin'){
            throw new \Exception("Unable to retrieve admin");
        }

        $this->admin_id = $admin_id;

        return $this;
    }

    public function setUserBlockId(int $user_block_id): TransactionBuilder
    {
        $user = UserBlock::find($user_block_id);
        if(!isset($user)){
            throw new \Exception("Unable to retrieve user block");
        }

        $this->user_block_id = $user_block_id;
        return $this;
    }

    public function setReferalInternalId(int $referal_internal_id): TransactionBuilder
    {
        $transaction = Transaction::query()->where('internal_id', $referal_internal_id)->first();
        if(!isset($transaction)){
            throw new \Exception("Unable to retrieve internal id");
        }

        $this->referal_internal_id = $referal_internal_id;
        return $this;
    }

    public function setRefundedAt(string $refunded_at): TransactionBuilder
    {
        $this->refunded_at = $refunded_at;
        return $this;
    }

    public function setIsGoalTransactionRefund(bool $is_goal_transaction_refund = false): TransactionBuilder
    {
        $this->is_goal_transaction_refund = $is_goal_transaction_refund;
        return $this;
    }

    public function setRefundedBy(int $refunded_by): TransactionBuilder
    {
        $user = User::query()->find($refunded_by);
        if(!isset($user)){
            throw new \Exception("Unable to retrieve refunded by id");
        }

        $this->refunded_by = $refunded_by;
        return $this;
    }

    public function setRefundType(string $refund_type): TransactionBuilder
    {
        $this->refund_type = $refund_type;
        return $this;
    }

    public function setTransactionTypeIdByType(string $type): TransactionBuilder
    {
        $transaction_type = TransactionType::query()->where('type', $type)->first();
        if(!isset($transaction_type)){
            throw new \Exception("Unable to retrieve transaction's type");
        }
        $this->transaction_type_id = $transaction_type->id;
        return $this;
    }

    public function setInternalStatus(string $internal_status): TransactionBuilder
    {
        $this->internal_status = $internal_status;
        return $this;
    }

    public function setInternalStatusReason(string $internal_status_reason): TransactionBuilder
    {
        $this->internal_status_reason = $internal_status_reason;
        return $this;
    }

    public function setInternalStatusBy(int $internal_status_by): TransactionBuilder
    {
        $user = User::find($internal_status_by);
        if(!isset($user)){
                throw new \Exception("Unable to retrieve internal status by");
        }
        $this->internal_status_by = $internal_status_by;
        return $this;
    }

    public function setNotes(string $notes = null): TransactionBuilder
    {
        $this->notes = $notes;
        return $this;
    }

    public function setAdminDescription(string $admin_description = null): TransactionBuilder
    {
        $this->admin_description = $admin_description;
        return $this;
    }

    public function hasPulse(bool $has_pulse): TransactionBuilder
    {
        $this->has_pulse = $has_pulse;
        return $this;
    }

    public function store(): TransactionBuilder
    {
        $transaction_attributes = [
            'internal_id' => $this->internal_id,
            'referal_internal_id' => $this->referal_internal_id,
            'rookie_id' => $this->rookie_id,
            'leader_id' => $this->leader_id,
            'type' => $this->type,
            'transaction_type_id' => $this->transaction_type_id,
            'morgi' => $this->morgi,
            'taxed_morgi' => $this->taxed_morgi,
            'taxed_micromorgi' => $this->taxed_micromorgi,
            'taxed_dollars' => $this->taxed_dollars,
            'micromorgi' => $this->micromorgi,
            'dollars' => $this->dollars,
            'notes' => $this->notes,
            'payment_rookie_id' => $this->payment_rookie_id,
            'subscription_id' => $this->subscription_id,
            'refund_type' => $this->refund_type,
            'admin_id' => $this->admin_id,
            'refunded_at' => $this->refunded_at,
            'leader_payment_id' => $this->leader_payment_id,
            'refunded_by' => $this->refunded_by,
            'admin_description' => $this->admin_description,
            'internal_status' => $this->internal_status,
            'internal_status_reason' => $this->internal_status_reason,
            'internal_status_by' => $this->internal_status_by,
            'user_block_id' => $this->user_block_id,
            'coupon_id' => $this->coupon_id,
            'goal_id' => $this->goal_id,
            'is_goal_transaction_refund' => $this->is_goal_transaction_refund
        ];

        $this->transaction = Transaction::create($transaction_attributes);

        if($this->has_pulse){
            NotificationUtils::morgiPulse();
        }

        return $this;
    }

    private function tax(float $amount): float
    {
        return ($amount * 0.9) * 0.75;
    }

    private function generateInternalId(): string
    {
        $internal_id = (string)rand(100, 999999999);

        if(!Transaction::where('internal_id', $internal_id)->first()){
            return $internal_id;
        }

        while (Transaction::where('internal_id', $internal_id)->exists()){
            $internal_id = (string)rand(100, 999999999);
        }

        return $internal_id;
    }
}
