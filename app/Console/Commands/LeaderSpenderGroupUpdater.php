<?php

namespace App\Console\Commands;

use App\Enums\JobHistoryEnum;
use App\Models\JobHistory;
use App\Models\Leader;
use App\Models\LeaderPayment;
use App\Models\SpenderGroup;
use DateTime;
use Illuminate\Console\Command;

class LeaderSpenderGroupUpdater extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaders:spenders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update leaders spenders';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $job_history = JobHistory::query()->create(['type' => JobHistoryEnum::SPENDER_GROUPS, 'start_at' => now(), 'end_at' => now()]);
        $leaders = Leader::query()
            ->select('leaders.*')
            ->join('users', 'users.id', '=', 'leaders.id')
            ->where('users.active', true)
            ->where('leaders.spender_group_forced_by_admin', false)
            ->get();

        $spender_groups = SpenderGroup::all();
        $matrix = [];

        foreach ($spender_groups as $spender_group){
            $matrix[$spender_group->id] = $spender_group;
        }

        foreach ($leaders as $leader){

            $total_charged = LeaderPayment::query()
                ->where('leader_id', $leader->id)
                ->where('status', 'paid')
                ->sum('dollar_amount');

            $created_at = new DateTime($leader->created_at);
            $now = new DateTime(now());
            $interval = $created_at->diff($now);

            $days_on_site = $interval->format('%a');
            $updated = false;

            foreach ($matrix as $values){

                if(!$updated
                    && $total_charged >= $values->total_charged
                    && $days_on_site >= $values->days_on_site
                    && $leader->spender_group_id < $values->id + 1) {

                    $leader->update(['spender_group_id' => $values->id + 1]);
                    $updated = true;
                }
            }
        }

        $job_history->update(['completed' => true, 'completed_at' => now()]);

        return 0;
    }
}
