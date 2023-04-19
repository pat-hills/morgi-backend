<?php

namespace App\Console\Commands;

use App\Enums\JobHistoryEnum;
use App\Models\JobHistory;
use App\Models\PubnubChannel;
use App\Models\Rookie;
use App\Models\RookieStats;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserActivityHistory;
use App\Models\UserBlock;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ComputeRookiesStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rookies:stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compute Rookies Stats';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $job_history = JobHistory::query()->create(['type' => JobHistoryEnum::ROOKIE_STATS, 'start_at' => now(), 'end_at' => now()]);

        $rookies = Rookie::query()->selectRaw(
            'rookies.*, users.joined_telegram_bot_at as joined_telegram_bot_at, users.description as sql_description,
            COUNT(photos.id) as photos_count, COUNT(videos.id) as videos_count,
            users.total_subscriptions_count as total_subscriptions_count
            ')
            ->join('users', 'users.id', '=', 'rookies.id')
            ->leftJoin('photos', 'rookies.id', '=', 'photos.user_id')
            ->leftJoin('videos', 'rookies.id', '=', 'videos.user_id')
            ->where('users.active', true)
            ->whereDate('users.last_activity_at', '>=', now()->subMonth()->toDateString())
            ->groupBy('rookies.id')
            ->get();

        $rookies_ids = $rookies->pluck('id')->toArray();

        $leaders_referred = User::query()
            ->whereIn('referred_by', $rookies_ids)
            ->where('type', 'leader')
            ->where('active', true)
            ->get();

        $active_subscriptions = Subscription::query()
            ->whereIn('rookie_id', $rookies_ids)
            ->whereIn('status', ['active', 'canceled'])
            ->whereNull('deleted_at')
            ->whereDate('valid_until_at', '>=', now()->toDateString())
            ->get();

        $leaders_sending_micromorgi_last_seven_days = Transaction::query()
            ->whereIn('rookie_id', $rookies_ids)
            ->where('type', 'chat')
            ->whereNull('refund_type')
            ->whereDate('created_at', '>=', Carbon::now()->subWeek())
            ->groupBy('leader_id')
            ->get();

        $avg_first_response_times = PubnubChannel::query()
            ->whereIn('rookie_id', $rookies_ids)
            ->whereNull('goal_id')
            ->get();

        $this->info("FETCHING {$rookies->count()} USERS | " . now()->toDateTimeString());
        $this->info("");

        $rookies_stats_to_insert = [];

        foreach ($rookies as $rookie){

            $leaders_blocked_ids = UserBlock::select('to_user_id')
                ->whereNull('deleted_at')
                ->where('from_user_id', $rookie->id)
                ->pluck('to_user_id')
                ->toArray();

            $rookie_stats = [
                'rookie_id' => $rookie->id,
                'created_at' => now(),
                'updated_at' => now()
            ];

            /*
             * logins_streak
             */
            $rookie_stats['logins_streak'] = $this->getLoginsStreak($rookie->id);


            /*
             * photos
             */
            $rookie_stats['photos'] = $rookie->photos_count;


            /*
             * videos
             */
            $rookie_stats['videos'] = $rookie->videos_count;


            /*
             * has_description
             */
            $rookie_stats['has_description'] = isset($rookie->sql_description);


            /*
             * joined_telegram_bot
             */
            $rookie_stats['joined_telegram_bot'] = isset($rookie->joined_telegram_bot_at);


            /*
             * first_micromorgi_gift_leaders
             */
            $rookie_stats['first_micromorgi_gift_leaders'] = $rookie->first_micromorgi_gift_leaders ?? 0;


            /*
             * leaders_referred
             */
            $rookie_stats['leaders_referred'] = $leaders_referred->where('referred_by', $rookie->id)->count();


            /*
             * active_subscriptions
             */
            $rookie_stats['active_subscriptions'] = $active_subscriptions->where('rookie_id', $rookie->id)
                ->whereNotIn('leader_id', $leaders_blocked_ids)
                ->count();


            /*
             * leaders_sending_micromorgi_last_seven_days
             */
            $rookie_stats['leaders_sending_micromorgi_last_seven_days'] = $leaders_sending_micromorgi_last_seven_days->where('rookie_id', $rookie->id)->count();


            /*
             * avg_first_response_time_seconds
             */
            $avg_first_response_times_null = $avg_first_response_times->where('rookie_id', $rookie->id)->whereNull('avg_response_time')->count();
            $avg_first_response_times_not_null = $avg_first_response_times->where('rookie_id', $rookie->id)->whereNotNull('avg_response_time')->count();
            $rookie_stats['avg_first_response_time_seconds'] = ($avg_first_response_times_not_null >= $avg_first_response_times_null)
                ? $avg_first_response_times->whereNotNull('avg_response_time')->where('rookie_id', $rookie->id)->avg('avg_response_time') ?? 0
                : 0;


            /*
             * avg_subscriptions_period
             */
            $subscriptions_period = [];
            $subscriptions = Subscription::query()
                ->where('rookie_id', $rookie->id)
                ->get();

            foreach ($subscriptions as $subscription){

                if($subscription->status==='active'){
                    $subscriptions_period[] = Carbon::now()->timestamp - strtotime($subscription->subscription_at);
                }

                $closed_at = (isset($subscription->canceled_at))
                    ? strtotime($subscription->canceled_at)
                    : strtotime($subscription->failed_at);

                $subscriptions_period[] = $closed_at - strtotime($subscription->subscription_at);
            }

            $avg_subscriptions_period = (count($subscriptions_period)>0) ? array_sum($subscriptions_period)/count($subscriptions_period) : 0;
            $rookie_stats['avg_subscriptions_period'] = $avg_subscriptions_period ?? 0;


            /*
             * avg_gifts_amounts_in_dollars
             */
            $total_dollars_amount = Transaction::query()
                ->where('rookie_id', $rookie->id)
                ->whereNotNull('leader_id')
                ->sum('dollars');

            $rookie_stats['avg_gifts_amounts_in_dollars'] = ($rookie->total_subscriptions_count>0)
                ? $total_dollars_amount/$rookie->total_subscriptions_count
                : 0;


            /*
             * leaders_retaining_rookie
             */
            $rookie_stats['leaders_retaining_rookie'] = $this->computeRetainingRookies($rookie->id, $leaders_blocked_ids);


            /*
             * hungry_rookies
             */
            $rookie_stats['hungry_rookies'] = $this->computeHungryRookies($rookie->id, $leaders_blocked_ids);


            /*
             * leaders_first_subscription
             */
            $rookie_stats['leaders_first_subscription'] = $rookie->leaders_first_subscription;

            /*
             * converters_subscriptions
             */
            $channels_count = PubnubChannel::query()->where('rookie_id', $rookie->id)->count();
            $rookie_stats['converters_subscriptions'] = ($channels_count <= 0 || $rookie->total_subscriptions_count <= 0)
                ? 0
                : (int)(($channels_count / $rookie->total_subscriptions_count) * 100);

            /*
             * time_to_subscribe
             */
            $rookie_stats['time_to_subscribe'] = PubnubChannel::query()
                    ->selectRaw('pubnub_channels.*')
                    ->join('subscriptions', 'subscriptions.id', '=', 'pubnub_channels.subscription_id')
                    ->where('subscriptions.status', 'active')
                    ->where('pubnub_channels.rookie_id', $rookie->id)
                    ->whereNotNull('pubnub_channels.time_to_subscribe')
                    ->avg('pubnub_channels.time_to_subscribe') ?? 0;

            $rookies_stats_to_insert[] = $rookie_stats;
        }

        RookieStats::query()
            ->whereIn('rookie_id', $rookies_ids)
            ->whereNull('deleted_at')
            ->update([
                'deleted_at' => now()
            ]);

        RookieStats::multiInsert($rookies_stats_to_insert);

        $this->info("FINISHED | " . now()->toDateTimeString());
        $job_history->update(['completed' => true, 'completed_at' => now()]);
        return 0;
    }

    private function getLoginsStreak(int $rookie_id): int
    {
        $activities = UserActivityHistory::query()
            ->selectRaw("EXTRACT(DAY FROM created_at) as day")
            ->where('user_id', $rookie_id)
            ->where('created_at', '>=', now()->subDays(10))
            ->groupByRaw("CAST(created_at AS DATE)")
            ->get();

        $counter = 0;
        $latest_activity_day = null;
        foreach ($activities as $activity){

            if(!isset($latest_activity_day)){
                $latest_activity_day = $activity->day;
                $counter++;
                continue;
            }

            if($latest_activity_day + 1 === $activity->day){
                $latest_activity_day = $activity->day;
                $counter++;
                continue;
            }

            $latest_activity_day = $activity->day;
            $counter = 1;
        }

        return $counter;
    }

    private function computeHungryRookies(int $rookie_id, array $leaders_blocked_ids): int
    {
        //Rookie has transactions?
        $rookie_has_transactions = Transaction::query()->where('rookie_id', $rookie_id)
            ->where('type', 'gift')
            ->whereNull('refund_type')
            ->whereNotIn('leader_id', $leaders_blocked_ids)
            ->exists();
        if(!$rookie_has_transactions){
            return 0;
        }

        $now_year = now()->year;
        $months = [];

        for ($i = 0; $i <= 3; $i++){
            $months[] = Transaction::query()->where('rookie_id', $rookie_id)
                ->where('type', 'gift')
                ->whereNull('refund_type')
                ->whereYear('created_at', $now_year)
                ->whereMonth('created_at', now()->subMonths($i)->month)
                ->count();
        }

        // If the rookie is new one passing 6th decile
        if(empty($months)){
            return 24;
        }

        $months = array_reverse($months);
        $first_month = $months[0];
        $count = 0;

        foreach ($months as $month){
            if($month >= $first_month){
                $count = $first_month;
                continue;
            }

            $count = $first_month - $month;
            break;
        }

        return $count;
    }

    private function computeRetainingRookies(int $rookie_id, array $leaders_blocked_ids): int
    {
        //Rookie has transactions?
        $rookie_has_transactions = Transaction::query()->where('rookie_id', $rookie_id)
            ->where('type', 'gift')
            ->whereNotIn('leader_id', $leaders_blocked_ids)
            ->whereNull('refund_type')
            ->exists();

        if(!$rookie_has_transactions){
            return 0;
        }

        $now_year = now()->year;

        $leaders_two_months_ago = Transaction::query()->where('rookie_id', $rookie_id)
            ->where('type', 'gift')
            ->whereNull('refund_type')
            ->whereYear('created_at', $now_year)
            ->whereMonth('created_at', now()->subMonths(2)->month)
            ->count();

        $leaders_one_months_ago = Transaction::query()->where('rookie_id', $rookie_id)
            ->where('type', 'gift')
            ->whereNull('refund_type')
            ->whereYear('created_at', $now_year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->count();

        // If the rookie is new one passing 6th decile
        if($leaders_one_months_ago === 0 || $leaders_two_months_ago === 0){
            return 24;
        }

        if($leaders_one_months_ago >= $leaders_two_months_ago){
            return $leaders_two_months_ago;
        }

        if($leaders_two_months_ago >= $leaders_one_months_ago){
            return $leaders_two_months_ago - $leaders_one_months_ago;
        }

        return 0;
    }
}
