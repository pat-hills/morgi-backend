<?php

namespace App\Transactions\Refund;

use App\Models\ActivityLog;
use App\Models\Transaction;
use App\Transactions\TransactionBuilder;
use App\Utils\ActivityLogsUtils;
use App\Utils\ReasonUtils;
use Carbon\Carbon;
use Exception;

class TransactionRefund extends TransactionBuilder
{
    public $type = 'refund';

    public static function create(Transaction $old_transaction, int $admin_id = null, string $reason = null, int $user_block_id = null): Transaction
    {
        $builder = new TransactionRefund();

        if ($old_transaction->refund_type === 'chargeback') {
            try {
                $builder->setTransactionTypeIdByType('chargeback');
            } catch (Exception $exception) {
                throw new Exception($exception->getMessage());
            }
        }

        if ($old_transaction->type === 'bonus') {
            try {
                $builder->setTransactionTypeIdByType('refund_bonus');
            } catch (Exception $exception) {
                throw new Exception($exception->getMessage());
            }
        }

        if (isset($user_block_id)) {
            try {
                $builder->setTransactionTypeIdByType('rookie_block_leader')
                    ->setUserBlockId($user_block_id);
            } catch (Exception $exception) {
                throw new Exception($exception->getMessage());
            }
        }

        if (isset($admin_id)) {
            try{
                $builder->setRefundedBy($admin_id);
            } catch (Exception $exception) {
                throw new Exception($exception->getMessage());
            }
        }

        if (isset($old_transaction->admin_id)) {
            try {
                $builder->setAdminId($old_transaction->admin_id);
            } catch (Exception $exception) {
                throw new Exception($exception->getMessage());
            }
        }

        if (isset($old_transaction->refund_type)) {
            $builder->setRefundType($old_transaction->refund_type);
        }

        if (isset($old_transaction->internal_id)) {
            try{
                $builder->setReferalInternalId($old_transaction->internal_id);
            } catch (Exception $exception) {
                throw new Exception($exception->getMessage());
            }
        }

        if (isset($old_transaction->rookie_id)) {
            try{
                $builder->setRookieId($old_transaction->rookie_id);
            } catch (Exception $exception) {
                throw new Exception($exception->getMessage());
            }
        }

        if (isset($old_transaction->leader_id)) {
            try{
                $builder->setLeaderId($old_transaction->leader_id);
            } catch (Exception $exception) {
                throw new Exception($exception->getMessage());
            }
        }

        if (isset($old_transaction->dollars)) {
            $builder->dollars = $old_transaction->dollars;
        }

        if (isset($old_transaction->morgi)) {
            $builder->morgi = $old_transaction->morgi;
        }

        if (isset($old_transaction->micromorgi)) {
            $builder->micromorgi = $old_transaction->micromorgi;
        }

        if (isset($old_transaction->taxed_dollars)) {
            $builder->taxed_dollars = $old_transaction->taxed_dollars;
        }

        if (isset($old_transaction->taxed_micromorgi)) {
            $builder->taxed_micromorgi = $old_transaction->taxed_micromorgi;
        }

        if (isset($old_transaction->taxed_morgi)) {
            $builder->taxed_morgi = $old_transaction->taxed_morgi;
        }

        if (isset($old_transaction->subscription_id)) {
            try{
                $builder->setSubscriptionId($old_transaction->subscription_id);
            } catch (Exception $exception) {
                throw new Exception($exception->getMessage());
            }
        }

        if (isset($old_transaction->goal_id)) {
            try{
                $builder->setGoalId($old_transaction->goal_id);
            } catch (Exception $exception) {
                throw new Exception($exception->getMessage());
            }
        }

        if (isset($old_transaction->coupon_id)) {
            try{
                $builder->setCouponId($old_transaction->coupon_id);
            } catch (Exception $exception) {
                throw new Exception($exception->getMessage());
            }
        }

        if (isset($old_transaction->payment_rookie_id)) {
            try{
                $builder->setPaymentRookieId($old_transaction->payment_rookie_id);
            } catch (Exception $exception) {
                throw new Exception($exception->getMessage());
            }
        }

        if (isset($old_transaction->leader_payment_id)) {
            try{
                $builder->setLeaderPaymentId($old_transaction->leader_payment_id);
            } catch (Exception $exception) {
                throw new Exception($exception->getMessage());
            }
        }

        if (isset($old_transaction->internal_status_reason)) {
            $builder->setInternalStatusReason($old_transaction->internal_status_reason);
        }

        if (isset($old_transaction->internal_status_by)) {
            try{
                $builder->setInternalStatusBy($old_transaction->internal_status_by);
            } catch (Exception $exception) {
                throw new Exception($exception->getMessage());
            }
        }

        if (isset($old_transaction->refund_type)) {
            $builder->setRefundType($old_transaction->refund_type);
        }

        if($old_transaction->type === 'goal'){
            $is_goal_transaction_refund = !Transaction::query()
                ->where('goal_id', $old_transaction->goal_id)
                ->where('rookie_id', $old_transaction->rookie_id)
                ->where('type', 'goal_withdraw')
                ->exists();
            if($is_goal_transaction_refund){
                $builder->setIsGoalTransactionRefund(true);
            }
        }

        try {
            $builder->setRefundedAt(Carbon::now()->toDateTimeString())
                ->setNotes(ReasonUtils::ALL_REASON[$reason] ?? $reason)
                ->setInternalStatus($old_transaction->internal_status)
                ->store()
                ->storeActivityLog();
        }catch(Exception $exception)  {
            throw new Exception($exception->getMessage());
        }

        return $builder->transaction;
    }

    private function storeActivityLog(): TransactionRefund
    {
        $this->activity_log = ActivityLog::create([
            'refund_type' => $this->refund_type,
            'initiated_by' => (isset($this->refunded_by)) ? 'morgi' : 'biller',
            'internal_id' => ActivityLogsUtils::generateInternalId($this->leader_id ?? $this->rookie_id),
            'transaction_internal_id' => $this->internal_id,
            'leader_id' => $this->leader_id,
            'rookie_id' => $this->rookie_id,
            'micromorgi' => $this->micromorgi,
            'morgi' => $this->morgi,
            'dollars' => $this->dollars,
            'admin_id' => $this->refunded_by ?? null,
            'refunded_at' => Carbon::now(),
        ]);

        return $this;
    }
}
