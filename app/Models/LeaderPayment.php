<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaderPayment extends Model
{
    use HasFactory;

    protected $table = 'leaders_payments';

    protected $fillable = [
        'leader_id',
        'amount',
        'status',
        'refund_date',
        'refund_reason',
        'leader_payment_method_id',
        'ccbill_subscriptionId',
        'ccbill_failureReason',
        'ccbill_failureCode',
        'dollar_amount',
        'currency_type',
        'ip_address',
        'subscription_id',
        'payment_country',
        'ccbill_transactionId',
        'type',
        'payload'
    ];

    protected $appends = [
        'is_refunded'
    ];

    protected $hidden = [
        'status', 'ccbill_subscriptionId',
        'ccbill_failureReason', 'ccbill_failureCode', 'ip_address', 'payload'
    ];


    public function getIsRefundedAttribute()
    {
        return $this->status === 'refunded';
    }

    public function isRebillNewCard()
    {
        if(!isset($this->subscription_id)){
            return false;
        }

        $previous_payment = LeaderPayment::where('subscription_id', $this->subscription_id)->where('created_at', '<', $this->created_at)->latest()->first();

        if(!isset($previous_payment)){
            return false;
        }

        return $this->leader_payment_method_id === $previous_payment->leader_payment_method_id;
    }

    public function refundMicromorgi(int $admin_id = null)
    {
        if($this->currency_type !== 'micro_morgi'){
            throw new \Exception("This is not a micromorgi transaction");
        }

        try {
            $leader_package = LeaderPackage::where('leader_payment_id', $this->id)->first();
            if(!isset($leader_package)){

                $this->update([
                    'status' => 'refunded',
                    'refund_type' => 'refund',
                    'refund_date' => now()
                ]);

                $transaction = Transaction::query()->where('leader_payment_id', $this->id)->first();
                if (isset($transaction)){
                    $transaction->update(['refund_type' => 'refund']);
                }
            }

            /*
             * Step 1 => Leader bought the package but spent 0
             */
            if($leader_package->amount === $leader_package->amount_available){

                $leader_package->update(['is_refunded' => true]);

                $leader = Leader::find($this->leader_id);
                $leader->popMicromorgi($leader_package->amount);

                $this->update([
                    'status' => 'refunded',
                    'refund_type' => 'refund',
                    'refund_date' => now()
                ]);

                $transaction = Transaction::query()->where('leader_payment_id', $this->id)->first();
                if (isset($transaction)){
                    $transaction->update(['refund_type' => 'refund']);
                }

                return;
            }

            /*
             * Step 2 => Leader bought the package and spent micromorgi
             */
            $leader_packages_transactions = LeaderPackageTransaction::query()
                ->where('leader_package_id', $leader_package->id)
                ->where('is_refunded', false)
                ->get();

            $leader = Leader::find($this->leader_id);

            foreach ($leader_packages_transactions as $leader_package_transaction){

                $transaction = Transaction::find($leader_package_transaction->transaction_id);
                if(!isset($transaction)){
                    continue;
                }

                $rookie = Rookie::find($transaction->rookie_id);
                if(!isset($rookie)){
                    continue;
                }

                $transaction->update([
                    'refund_type' => 'refund',
                    'refunded_by' => $admin_id,
                    'refunded_at' => now()
                ]);

                if(isset($transaction->goal_id) && $transaction->type === 'goal'){
                    GoalDonation::query()->where('goal_id', $transaction->goal_id)
                        ->where('transaction_id', $transaction->id)
                        ->update(['status' => 'refund']);
                }

                $transaction->refresh();

                \App\Transactions\Refund\TransactionRefund::create($transaction, $admin_id, 'System Micro Morgi package refund');

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

        }catch (\Exception $exception){
            throw new \Exception("Internal server error, please try later");
        }

    }

    public function getSubscriptionHistoryAttribute()
    {
        $select = [
            'leaders_payments.status',
            'leaders_payments.amount',
            'leaders_payments.created_at'
        ];

        switch ($this->type) {
            case 'first_purchase':

                $query = LeaderPayment::where('id', $this->id);
                break;

            case 'rebill':

                $query = LeaderPayment::where('subscription_id', $this->subscription_id);
                break;

            default:
                return false;
        }

        $leaders_payments = $query->select($select)
            ->get()
            ->makeHidden(['is_refunded'])
            ->makeVisible(['status']);

        return [
            'id' => $this->id,
            'morgi' => $leaders_payments->where('status', 'paid')->sum('amount'),
            'count' => count($leaders_payments),
            'payments' => $leaders_payments
        ];
    }
}
