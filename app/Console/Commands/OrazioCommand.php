<?php

namespace App\Console\Commands;

use App\Models\RookieSeen;
use App\Models\User;
use App\Orazio\OrazioHandler;
use Illuminate\Console\Command;

class OrazioCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaders:orazio';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command call orazio algoritm';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /*
         * Empty rookies seen for leaders who have the latest calculation older than 3 days
         */
        $this->alert('Searching leaders to refresh..');
        $leaders_to_refresh = User::query()->selectRaw("users.id as id")
            ->leftJoin('rookies_seen', 'rookies_seen.leader_id', '=', 'users.id')
            ->where('users.type', 'leader')
            ->where('users.active', true)
            ->whereDate('users.last_activity_at', '>=', now()->subWeek()->toDateString())
            ->whereDate('rookies_seen.created_at', '<=', now()->subDays(3)->toDateString())
            ->havingRaw("COUNT(rookies_seen.id) > 0")
            ->groupBy('users.id')
            ->orderByDesc('users.last_activity_at')
            ->get();

        if($leaders_to_refresh->count() > 0){
            RookieSeen::query()->whereIn('leader_id', $leaders_to_refresh->pluck('id'))->delete();
        }

        $this->info('Refreshed leaders: ' . $leaders_to_refresh->count());

        /*
         * Run orazio
         */
        $this->alert('Searching leaders..');
        $leaders = User::query()->selectRaw("users.id as id")
            ->leftJoin('rookies_seen', 'rookies_seen.leader_id', '=', 'users.id')
            ->where('users.type', 'leader')
            ->where('users.active', true)
            ->whereDate('users.last_activity_at', '>=', now()->subWeek()->toDateString())
            ->havingRaw("COUNT(rookies_seen.id) < 10")
            ->groupBy('users.id')
            ->orderByDesc('users.last_activity_at')
            ->get();

        $this->info('Started Orazio..');
        foreach ($leaders as $leader){
            try {
                OrazioHandler::freshSeen($leader->id, 'Orazio cronjob', true);
                $this->info("Leader {$leader->id} - Fetched");
            }catch (\Exception $exception){
                $this->error("Leader {$leader->id} - {$exception->getMessage()}");
            }
        }

        $this->info('Done!');
        return 0;
    }
}
