<?php

namespace App\Console\Commands;

use App\Enums\JobHistoryEnum;
use App\Models\ActivityLog;
use App\Models\JobHistory;
use App\Models\Payment;
use App\Models\PaymentPeriod;
use App\Models\Rookie;
use App\Models\Transaction;
use App\Models\PaymentRookie;
use App\Models\UserIdentityDocument;
use App\Transactions\Withdrawal\TransactionRookieWithdrawalPending;
use App\Utils\NotificationUtils;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreatePaymentPeriod extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment_period:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "This command create rookie's payment period rows";


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!$this->hasToRun()){
            $this->error("This job must run only the 15 and the last of the month!");
            return 0;
        }

        $job_history = JobHistory::query()->create(['type' => JobHistoryEnum::PAYMENT_PERIOD, 'start_at' => now(), 'end_at' => now()]);

        $start_date = PaymentPeriod::getLastDate();
        $end_date = date('Y-m-d');

        if(!isset($start_date)){
            $start_date = date('Y-m') . "-01";
        }

        if($start_date === $end_date){
            $this->error("This job already runned today");
            return 0;
        }

        $payment_period = PaymentPeriod::create([
            'name' => "From $start_date to $end_date",
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);

        $this->alert("Payment Period created!");

        /*
         * rookie_id, reference, amount
         */
        $payments_to_do = [];

        $users = Rookie::query()
            ->leftJoin('users', 'users.id', '=', 'rookies.id')
            ->where('users.active', true)
            ->where('users.status', '!=','deleted')
            ->where('rookies.untaxed_withdrawal_balance', '>', 0)
            ->groupBy('rookies.id')
            ->get();

        $this->info("Rookies fetched: " . $users->count());

        $latest_rookies_payments = Transaction::query()
            ->where('type', 'withdrawal')
            ->whereIn('rookie_id', $users->pluck('id'))
            ->get();

        foreach($users as $user){

            /*
             * MIN MORGI BALANCE NOT REACHED
            */
            if ($user->morgi_balance < 0 || $user->micro_morgi_balance < 0 || $user->untaxed_withdrawal_balance <= env('MIN_MORGI_BALANCE', 50)) {
                NotificationUtils::sendNotification($user->id, 'rookie_rejected_payment_min_usd', now(), [
                    'amount' => env('MIN_MORGI_BALANCE', 50)
                ]);
                $this->error("Rookie {$user->id} has {$user->untaxed_withdrawal_balance}/50 balance");
                continue;
            }

            /*
             * NOT UPLOADED ID CARD
             */
            $identity_card_exists = UserIdentityDocument::query()->where('user_id', $user->id)->exists();
            if (!$identity_card_exists) {
                NotificationUtils::sendNotification($user->id, 'rookie_rejected_payment_id_card', now());
                $this->error("Rookie {$user->id} does not have id card approved");
                continue;
            }

            /*
             * NO PAYMENT METHOD FOUND
             */
            $payment_method = $user->getMainPaymentMethod();
            if (!isset($payment_method)) {
                NotificationUtils::sendNotification($user->id, 'rookie_rejected_payment_no_method', now());
                $this->error("Rookie {$user->id} does not have payment method");
                continue;
            }

            /*
             * EMPTY REFERENCE
             */
            $reference = $payment_method->payment_info;
            if (!isset($reference)) {
                NotificationUtils::sendNotification($user->id, 'rookie_rejected_payment_no_method', now());
                $this->error("Rookie {$user->id} does not have payment method reference or is empty");
                continue;
            }

            /*
             * ROOKIE MUST HAS MORGI GIFT FROM LEADER
             */
            $last_payment = $latest_rookies_payments->where('rookie_id', $user->id)->last();
            if(!$this->hasRookieMinMorgiGift($user, $last_payment)){
                continue;
            }

            $payments_to_do[$payment_method->payment_platform_id][] = [
                'rookie_id' => $user->id,
                'amount' => $user->withdrawal_balance,
                'reference' => $reference,
                'status' => 'pending'
            ];
        }

        foreach($payments_to_do as $payment_platform_id => $payment_platform_to_do){

            $platform_amount = array_sum(
                array_column($payment_platform_to_do, 'amount')
            );

            $payment = Payment::create([
                'payment_period_id' => $payment_period->id,
                'payment_platform_id' => $payment_platform_id,
                'amount' => $platform_amount
            ]);

            $this->alert("Created payment for platform: " . $payment_platform_id);

            foreach($payment_platform_to_do as $payment_to_do){

                $payment_to_do['payment_id'] = $payment->id;
                $payment_rookie = PaymentRookie::create($payment_to_do);
                $rookie = Rookie::query()->find($payment_to_do['rookie_id']);

                DB::beginTransaction();
                try {
                    TransactionRookieWithdrawalPending::create($payment_rookie->rookie_id, $payment_rookie->id);
                    DB::commit();
                }catch (\Exception $e){
                    DB::rollBack();
                    $this->error("Error during the creation of the transaction for rookie: $rookie->id");
                }

                $this->info("Created withdrawal transaction for rookie: " . $payment_rookie->rookie_id);
            }
        }

        $payment_period->update(['created_at' => now()]);
        $job_history->update(['completed' => true, 'completed_at' => now()]);

        $this->info("Job completed!");
        return 0;
    }

    private function hasToRun(): bool
    {
        $today = Carbon::now()->day;
        $last_day_of_the_current_month = Carbon::now()->lastOfMonth()->day;

        /*
         * This job has to run only the 15 and the last day of the month
         */
        return $today === $last_day_of_the_current_month || $today === 15;
    }

    private function hasRookieMinMorgiGift($user, $last_payment = null): bool
    {
        $last_payment_date = (isset($last_payment))
            ? $last_payment->created_at
            : null;

        $transactions_query = Transaction::query()
            ->where('type', 'gift')
            ->where('rookie_id', $user->id)
            ->whereNotNull('leader_id')
            ->whereNull('refund_type');

        if (isset($last_payment_date)) {
            $transactions_query->whereDate('created_at', '>', $last_payment_date);
        }

        $transactions = $transactions_query->get();

        return ($transactions->sum('morgi') >= env('MIN_ROOKIE_PAYMENT_LEADER_MORGI', 10));
    }
}
