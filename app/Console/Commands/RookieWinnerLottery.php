<?php

namespace App\Console\Commands;

use App\Enums\JobHistoryEnum;
use App\Models\JobHistory;
use App\Models\RookieWinnerHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RookieWinnerLottery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rookies:lottery';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Select 3 random active Rookies and give they a Morgi bonus!';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /**
         * Temporary stopped by Ron
         */
        return 0;
        $job_history = JobHistory::query()->create(['type' => JobHistoryEnum::ROOKIE_WINNER, 'start_at' => now(), 'end_at' => now()]);

        /*
         * Check if the job already runned today
         */

        $this->info('Check if that job already runned today..');
        $today_winners = RookieWinnerHistory::query()->whereDate('win_at', now())->exists();

        if($today_winners){
            $this->error('That job already runned today!');
            return 0;
        }

        /*
         * Selecting 3 lucky rookies and store it
         */
        $this->info('Retrieve of 3 random lucky rookies..');

        $morgi_amount = env('MORGI_WIN_AMOUNT_FOR_RING_LOTTERY', 100);
        $last_winners_rookies_ids = RookieWinnerHistory::get()->pluck('rookie_id')->toArray();

        $users = User::query()->selectRaw("users.id")
            ->join('photos', 'users.id', '=', 'photos.user_id')
            ->where('users.type', 'rookie')
            ->whereNotIn('users.id', $last_winners_rookies_ids)
            ->whereDate('users.last_activity_at', '>=', now()->subMonth()->toDateString())
            ->where('users.active', true)
            ->where('photos.main', true)
            ->groupBy('users.id')
            ->get();

        if($users->count()<3){
            $this->error('There are no rookie available!');
            return 0;
        }

        $new_winners_ids = $users->random(3)->pluck('id')->toArray();

        foreach ($new_winners_ids as $user_id){
            RookieWinnerHistory::create(['rookie_id' => $user_id, 'amount' => $morgi_amount, 'win_at' => now()]);
        }

        $job_history->update(['completed' => true, 'completed_at' => now()]);
        return 0;
    }
}
