<?php

namespace App\Console\Commands;

use App\Enums\JobHistoryEnum;
use App\Models\Complaint;
use App\Models\JobHistory;
use Illuminate\Console\Command;

class ComplaintFollowUpReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'complaint:follow-up-reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set null complaint that has expired follow up time';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $job_history = JobHistory::query()->create(['type' => JobHistoryEnum::COMPLAINT_FOLLOW_UP, 'start_at' => now(), 'end_at' => now()]);

        $today = now()->toDateTimeString();
        $complaints = Complaint::whereNotNull('follow_up')->get();

        foreach ($complaints as $complaint){
            if($complaint->follow_up <= $today){
                $complaint->update(['follow_up' => null]);
            }
        }

        $job_history->update(['completed' => true, 'completed_at' => now()]);

        return 0;
    }
}
