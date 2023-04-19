<?php

namespace App\Utils;

use App\Ccbill\CcbillUtils;
use App\Enums\LeaderPaymentEnum;
use App\Enums\TransactionRefundEnum;
use App\Models\GoalDonation;
use App\Models\Leader;
use App\Models\LeaderPackage;
use App\Models\LeaderPackageTransaction;
use App\Models\LeaderPayment;
use App\Models\PubnubChannel;
use App\Models\Rookie;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\TransactionRefund;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TransactionRefundUtils
{
    public $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public static function config(Transaction $transaction)
    {
        return new TransactionRefundUtils($transaction);
    }

    public static function createTransactionRefund(Transaction $transaction, string $refund_reason = null, int $admin_id = null): void
    {
        $transaction_refund = TransactionRefund::query()->create([
            'transaction_id' => $transaction->id,
            'admin_id' => $admin_id,
            'refund_reason' => $refund_reason
        ]);

        $leader_payment = LeaderPayment::query()->find($transaction->leader_payment_id);
        if(!isset($leader_payment)){
            throw new \Exception("Leader Payment not found");
        }

        try {
            CcbillUtils::refund($leader_payment->ccbill_subscriptionId, $leader_payment->id);
        }catch (\Exception $exception){
            $error = $exception->getMessage();
            $transaction_refund->fail($error);
            throw new \Exception($error);
        }
    }

    public static function createOrUpdateApprovedTransactionRefund(Transaction $transaction): void
    {
        $transaction_refund = TransactionRefund::query()->where('transaction_id', $transaction->id)->latest()->first();

        /*
         * Handle Update
         */
        if(isset($transaction_refund)){

            $transaction_refund->approve();
            $transaction->update([
                'admin_id' => $transaction_refund->admin_id,
                'notes' => $transaction_refund->refund_reason,
                'refunded_at' => now()
            ]);
            $leader_payment = LeaderPayment::query()->find($transaction->leader_payment_id);

            if (isset($leader_payment)) {
                $leader_payment->update([
                    'status' => LeaderPaymentEnum::STATUS_REFUNDED,
                    'refund_reason' => $transaction_refund->refund_reason,
                    'refunded_by' => $transaction_refund->admin_id,
                    'refund_date' => now(),
                ]);
            }
            return;
        }

        TransactionRefund::query()->create([
            'transaction_id' => $transaction->id,
            'status' => TransactionRefundEnum::STATUS_APPROVED,
            'admin_id' => $transaction_refund->admin_id,
            'approved_at' => now()
        ]);
    }

    public static function createOrUpdateFailedTransactionRefund(Transaction $transaction, string $error = null): void
    {
        $transaction_refund = TransactionRefund::query()->where('transaction_id', $transaction->id)->latest()->first();

        /*
         * Handle Update
         */
        if(isset($transaction_refund)){
            $transaction_refund->fail($error);
            return;
        }

        TransactionRefund::create([
            'transaction_id' => $transaction->id,
            'admin_id' => $transaction_refund->admin_id,
            'status' => TransactionRefundEnum::STATUS_FAILED,
            'failed_at' => now()
        ]);
    }

    public function refund($admin_id = null, $notes = null, $is_chargeback = false, $user_block_id = null)
    {
        if(isset($this->transaction->refund_type)) {
            return;
        }

        try {
            switch ($this->transaction->type) {
                case 'bought_micromorgi':
                    $this->refundBoughtMicromorgi($admin_id, $notes, $is_chargeback);
                    break;
                case 'chat':
                case 'goal':
                    $this->refundChatAndGoal($admin_id, $notes);
                    break;
                case 'bonus':

                    if(isset($this->transaction->rookie_id)) {
                        if(isset($this->transaction->morgi)) {
                            $this->refundMorgiBonusRookie($admin_id, $notes);
                            break;
                        }

                        if(isset($this->transaction->micromorgi)){
                            $this->refundMicroMorgiBonusRookie($admin_id, $notes);
                            break;
                        }
                        break;
                    }

                    $this->refundBonusLeader($admin_id, $notes);
                    break;
                case 'gift':
                    $this->refundGift($admin_id, $notes, $is_chargeback, $user_block_id);
                    break;
                default:
                    throw new \Exception("Error to compute refund");
            }
        }catch (\Exception $exception) {
            throw new \Exception("Error to refund. Details: {$exception->getMessage()}");
        }
    }

    private function refundBoughtMicromorgi($admin_id = null, $notes = null, $is_chargeback = false)
    {
        $leader_payment = LeaderPayment::query()->find($this->transaction->leader_payment_id);
        if(!isset($leader_payment)) {
            throw new \Exception("Leader's payment not found");
        }

        try {
            $leader_payment->refundMicromorgi($admin_id);
            $leader_payment->update([
                'status' => 'refunded',
                'refund_reason' => $notes,
                'refund_date' => now(),
                'refund_by' => $admin_id,
            ]);

            $this->transaction->update([
                'refunded_by' => $admin_id,
                'refunded_at' => now(),
                'refund_type' => $this->getNewRefundType($is_chargeback)
            ]);
            \App\Transactions\Refund\TransactionRefund::create($this->transaction, $admin_id, $notes);

        }catch (\Exception $exception) {
            $leader_payment->update(['status' => 'error_to_refund']);
            throw new \Exception("Transaction refunded by CCBill, unable to update transaction row, contact dev team! " . $exception->getMessage());
        }
    }

    private function refundChatAndGoal($admin_id = null, $notes = null)
    {
        try {
            $this->refundChatMicromorgiTransaction($admin_id);
            $this->transaction->update([
                'refunded_by' => $admin_id,
                'refunded_at' => now(),
                'refund_type' => $this->getNewRefundType()
            ]);
            \App\Transactions\Refund\TransactionRefund::create($this->transaction, $admin_id, $notes);

        } catch (\Exception $exception) {
            throw new \Exception("Error to refund this transaction, try later");
        }
    }

    private function refundMorgiBonusRookie($admin_id, $notes = null)
    {
        $rookie = Rookie::query()->find($this->transaction->rookie_id);

        if(!isset($rookie)) {
            throw new \Exception("Rookie not found");
        }

        try {
            $rookie->popMorgi($this->transaction->morgi, $this->transaction->taxed_morgi);
            $rookie->popDollars($this->transaction->dollars, $this->transaction->taxed_dollars);

            $this->transaction->update([
                'refunded_by' => $admin_id,
                'refunded_at' => now(),
                'refund_type' => $this->getNewRefundType()
            ]);
            \App\Transactions\Refund\TransactionRefund::create($this->transaction, $admin_id, $notes);

        }catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    private function refundMicroMorgiBonusRookie($admin_id, $notes = null)
    {
        $rookie = Rookie::query()->find($this->transaction->rookie_id);
        if(!isset($rookie)) {
            throw new \Exception("Rookie not found");
        }

        try {
            $rookie->popMicromorgi($this->transaction->micromorgi, $this->transaction->micromorgi);
            $rookie->popDollars($this->transaction->dollars, $this->transaction->dollars);

            $this->transaction->update([
                'refunded_by' => $admin_id,
                'refunded_at' => now(),
                'refund_type' => $this->getNewRefundType()
            ]);
            \App\Transactions\Refund\TransactionRefund::create($this->transaction, $admin_id, $notes);

        }catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    private function refundBonusLeader($admin_id, $notes = null)
    {
        try {
            $this->refundMicromorgiBonus($admin_id);
        } catch (\Exception $exception) {
            throw new \Exception("Error to refund this transaction, try later");
        }

        try {
            $this->transaction->update([
                'refunded_by' => $admin_id,
                'refunded_at' => now(),
                'refund_type' => $this->getNewRefundType()
            ]);
            \App\Transactions\Refund\TransactionRefund::create($this->transaction, $admin_id, $notes);
        }catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    private function refundGift($admin_id = null, $notes = null, $is_chargeback = false, $user_block_id = null)
    {
        $subscription = Subscription::query()->find($this->transaction->subscription_id);
        if(!isset($subscription)) {
            throw new \Exception("This is not a gift transaction");
        }

        try {
            $rookie = Rookie::query()->find($this->transaction->rookie_id);
            $rookie->popMorgi($this->transaction->morgi, $this->transaction->taxed_morgi);
            $rookie->popDollars($this->transaction->dollars, $this->transaction->taxed_dollars);

            $last_month = date('y-m-d H:i', strtotime('-1 months'));

            if(date('y-m-d H:i', strtotime($subscription->subscription_at)) > $last_month) {
                $subscription->update([
                    'canceled_at' => now(),
                    'status' => 'canceled',
                    'sent_reply_reminder_email_at' => null,
                    'deleted_at' => now(),
                    'valid_until_at' => now()
                ]);
                PubnubChannel::where('subscription_id', $subscription->id)->update(['active' => false]);
            }

            $this->transaction->update([
                'refunded_by' => $admin_id, 'refunded_at' => now(),
                'refund_type' => $this->getNewRefundType($is_chargeback)
            ]);
            \App\Transactions\Refund\TransactionRefund::create($this->transaction, $admin_id, $notes, $user_block_id);

        }catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    private function getNewRefundType($is_chargeback = false)
    {
        if($is_chargeback) {
            return 'chargeback';
        }

        return (strtotime($this->transaction->created_at) > Carbon::now()->subDay()->timestamp) ? 'void' : 'refund';
    }

    public function refundMicromorgiBonus($admin_id = null)
    {
        if($this->transaction->type!=='bonus' || !isset($this->transaction->micromorgi)) {
            throw new \Exception("This is not a micromorgi transaction");
        }

        if(isset($this->transaction->refund_type)) {
            return;
        }

        try {
            $leader_package = LeaderPackage::query()->where('transaction_id', $this->transaction->id)->first();
            if(!isset($leader_package)){
                return;
            }

            //Step 1 => Leader got the package but spent 0
            if($leader_package->amount === $leader_package->amount_available) {
                $leader_package->update(['is_refunded' => true]);

                $leader = Leader::find($this->transaction->leader_id);
                $leader->popMicromorgi($leader_package->amount);

                $this->transaction->update([
                    'refund_type' => 'refund',
                    'refunded_by' => $admin_id,
                    'refunded_at' => now()
                ]);
                return;
            }

            //Step 2 => Leader bought the package and spent micromorgi
            $leader_packages_transactions = LeaderPackageTransaction::query()
                ->where('leader_package_id', $leader_package->id)
                ->where('is_refunded', false)
                ->get();

            $leader = Leader::find($this->transaction->leader_id);

            foreach ($leader_packages_transactions as $leader_package_transaction) {

                $transaction = Transaction::find($leader_package_transaction->transaction_id);
                if(!isset($transaction)) {
                    continue;
                }

                $rookie = Rookie::find($transaction->rookie_id);
                if(!isset($rookie)) {
                    continue;
                }

                $transaction->update([
                    'refund_type' => 'refund',
                    'refunded_by' => $admin_id,
                    'refunded_at' => now()
                ]);

                if(isset($transaction->goal_id) && $transaction->type === 'goal') {
                    GoalDonation::query()
                        ->where('goal_id', $transaction->goal_id)
                        ->where('transaction_id', $transaction->id)
                        ->update(['status' => 'refund']);
                }

                $leader_package_transaction->update(['is_refunded' => true]);

                $leader->popMicromorgi($leader_package->amount);

                $has_to_pop_rookie_balance = true;

                /*
                 * Refunding a goal transaction, we have to pop rookie's balance
                 * only if the goal is ended and rookie received the goal's withdraw
                 */
                if($transaction->type === 'goal'){
                    $withdrawal_transaction_exists = Transaction::query()
                        ->where('goal_id', $transaction->goal_id)
                        ->where('rookie_id', $rookie->id)
                        ->where('type', 'goal_withdraw')
                        ->exists();
                    $has_to_pop_rookie_balance = $withdrawal_transaction_exists;
                }

                if($has_to_pop_rookie_balance){
                    $rookie->popMicromorgi($transaction->micromorgi, $transaction->taxed_micromorgi);
                    $rookie->popDollars($transaction->dollars, $transaction->taxed_dollars);
                }
            }

            $leader_package->update(['is_refunded' => true]);

        }catch (\Exception $exception) {
            throw new \Exception("Internal server error, please try later");
        }
    }

    public function refundChatMicromorgiTransaction($admin_id = null)
    {
        if(!in_array($this->transaction->type, ['chat', 'goal'])) {
            throw new \Exception("This is not a micromorgi transaction");
        }

        if(isset($this->transaction->refund_type)) {
            return;
        }

        try {
            $leader = Leader::find($this->transaction->leader_id);
            $rookie = Rookie::find($this->transaction->rookie_id);

            if(!isset($leader) || !isset($rookie)) {
                throw new \Exception("Rookie or Leader of this transaction does not exits");
            }

            $leader_packages_transactions = LeaderPackageTransaction::query()->where('transaction_id', $this->transaction->id)->get();

            if($leader_packages_transactions->count()===0) {
                throw new \Exception("Error during package's refund");
            }

            foreach ($leader_packages_transactions as $leader_package_transaction) {

                $leader_package = LeaderPackage::find($leader_package_transaction->leader_package_id);
                if(!isset($leader_package)) {
                    continue;
                }

                $leader_package_transaction->update(['is_refunded' => true]);
                $leader_package->update([
                    'amount_available' => $leader_package->amount_available + $leader_package_transaction->amount,
                    'amount_spent' => $leader_package->amount_spent - $leader_package_transaction->amount
                ]);

                $leader->update([
                    'micro_morgi_balance' => $leader->micro_morgi_balance + $leader_package_transaction->amount
                ]);

                $has_to_pop_rookie_balance = true;

                /*
                 * Refunding a goal transaction, we have to pop rookie's balance
                 * only if the goal is ended and rookie received the goal's withdraw
                 */
                if($this->transaction->type === 'goal'){
                    $withdrawal_transaction_exists = Transaction::query()
                        ->where('goal_id', $this->transaction->goal_id)
                        ->where('rookie_id', $rookie->id)
                        ->where('type', 'goal_withdraw')
                        ->exists();
                    $has_to_pop_rookie_balance = $withdrawal_transaction_exists;
                }

                if($has_to_pop_rookie_balance){
                    $rookie->popMicromorgi($this->transaction->micromorgi, $this->transaction->taxed_micromorgi);
                    $rookie->popDollars($this->transaction->dollars, $this->transaction->taxed_dollars);
                }
            }

            if(isset($this->transaction->goal_id) && $this->transaction->type === 'goal') {
                GoalDonation::query()
                    ->where('goal_id', $this->transaction->goal_id)
                    ->where('transaction_id', $this->transaction->id)
                    ->update(['status' => 'refund']);
            }

            $this->transaction->update([
                'refund_type' => 'refund',
                'refunded_by' => $admin_id,
                'refunded_at' => now()
            ]);
        }catch (\Exception $exception) {
            throw new \Exception("Internal server error, please try later");
        }
    }
}
