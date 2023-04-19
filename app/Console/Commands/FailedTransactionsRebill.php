<?php

namespace App\Console\Commands;

use App\Ccbill\CcbillUtils;
use App\Enums\JobHistoryEnum;
use App\Models\JobHistory;
use App\Models\PubnubChannel;
use App\Models\Subscription;
use App\Models\TransactionFailed;
use App\Utils\NotificationUtils;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FailedTransactionsRebill extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:failed-rebill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Attempt to rebill failed transactions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $job_history = JobHistory::query()->create(['type' => JobHistoryEnum::FAILED_TRANSACTIONS_REBILL, 'start_at' => now(), 'end_at' => now()]);

        /*
         * REMOVE OLD FAILED TRANSACTIONS
         */

        $this->info('Removing old failed transactions..');
        $failed_transactions = TransactionFailed::query()->select('id', 'subscription_id')
            ->where('attempts', '>=', env('TRANSACTIONS_MAX_ATTEMPTS', 3));

        $this->info("Removing {$failed_transactions->count()} transactions failed");

        $failed_transactions_ids = $failed_transactions->pluck('id')->toArray();

        $subscriptions_to_delete = Subscription::whereIn('id', $failed_transactions->pluck('subscription_id'))->get();

        foreach ($subscriptions_to_delete as $subscription_to_delete){

            $subscription_to_delete->update(['status' => 'failed']);
            PubnubChannel::query()->where('subscription_id', $subscription_to_delete->id)->update(['active' => false]);

            NotificationUtils::sendNotification($subscription_to_delete->leader_id, "invalid_card_subscription_canceled", now(), [
                'ref_user_id' => $subscription_to_delete->rookie_id
            ]);

            NotificationUtils::sendNotification($subscription_to_delete->rookie_id, "leader_canceled_subscription", now(), [
                'ref_user_id' => $subscription_to_delete->leader_id,
            ]);
        }

        TransactionFailed::whereIn('id', $failed_transactions_ids)->delete();


        /*
         * ATTEMPT REBILLS
         */

        $this->info('Started attempting rebills..');
        $subscriptions_to_rebill = TransactionFailed::query()->select('subscription_id')
            ->whereDate('last_attempt_at', '<=', Carbon::now()->subDay())
            ->get()->toArray();

        $subscriptions = Subscription::query()->findMany($subscriptions_to_rebill);

        foreach ($subscriptions as $subscription){

            if($subscription->status==='canceled'){
                TransactionFailed::where('subscription_id', $subscription->id)->delete();
                continue;
            }

            DB::beginTransaction();
            try {
                CcbillUtils::rebill($subscription, true);
                DB::commit();
            }catch (\Exception $exception){
                DB::rollBack();
                $this->info("{$subscription->id} went in error, CCBill error: $exception");
            }
        }

        $this->info("Attempted {$subscriptions->count()} rebills!");
        $job_history->update(['completed' => true, 'completed_at' => now()]);

        return 0;
    }
}
