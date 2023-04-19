<?php

namespace App\Console\Commands;

use App\Models\Broadcast;
use App\Models\BroadcastMessage;
use App\Models\Goal;
use App\Utils\BroadcastUtils;
use App\Utils\Goal\GoalUtils;
use Illuminate\Console\Command;

class GoalDeadlineCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'goal:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Help update goal\'s status';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $goals = Goal::query()->where('status', Goal::STATUS_ACTIVE)
            ->withSum('successfulDonations', 'amount')
            ->where('end_date', '<=', now())
            ->with(['rookie'])
            ->get();

        foreach($goals as $goal){

            if (!empty($goal->successful_donations_sum_amount)) {
                $goal_donations_percentage = ($goal->successful_donations_sum_amount / $goal->target_amount) * 100;
                if ($goal_donations_percentage >= Goal::MINIMUM_SUCCESS_PERCENTAGE) {
                    $broadcast = Broadcast::query()->firstOrCreate([
                        'is_goal' => true,
                        'sender_id' => $goal->rookie->id,
                        'display_name' => $goal->name
                    ]);

                    $goal->update(['status' => Goal::STATUS_AWAITING_PROOF]);
                    continue;
                }
            }

            try {
                if (!empty($goal->successful_donations_sum_amount)) {
                    GoalUtils::refundGoalDonations($goal->id);
                }
                $goal->update([
                    'status' => Goal::STATUS_CANCELLED,
                    'cancelled_reason' => Goal::CANCEL_REASON_GOAL_NOT_REACHED
                ]);
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }

        return 0;
    }
}
