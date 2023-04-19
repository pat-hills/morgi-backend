<?php

namespace App\Console\Commands;

use App\Ccbill\CcbillUtils;
use App\Enums\JobHistoryEnum;
use App\Models\JobHistory;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SubscriptionsRebill extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:rebill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rebill subscriptions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $job_history = JobHistory::query()->create(['type' => JobHistoryEnum::SUBSCRIPTIONS_REBILL, 'start_at' => now(), 'end_at' => now()]);

        $this->info('Started subscriptions rebills..');
        $subscriptions = Subscription::query()
            ->where('status', 'active')
            ->whereDate('next_donation_at', Carbon::now()->toDateString())
            ->whereNull('deleted_at')
            ->get();

        $this->info("Subscriptions found: {$subscriptions->count()}");

        foreach ($subscriptions as $subscription){
            DB::beginTransaction();
            try {
                CcbillUtils::rebill($subscription);
                DB::commit();
            }catch (\Exception $exception){
                DB::rollBack();
                $this->error("{$subscription->id} went in error, CCBill error: " . $exception->getMessage());
            }
        }

        $this->info('Subscriptions rebills completed!');
        $job_history->update(['completed' => true, 'completed_at' => now()]);

        return 0;
    }
}
