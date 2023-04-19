<?php

namespace App\Console\Commands;

use App\Enums\JobHistoryEnum;
use App\Enums\RookieScoreEnum;
use App\Models\JobHistory;
use App\Models\RookieScore;
use App\Models\RookieStats;
use App\Models\Subscription;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ComputeRookieScore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rookies:score';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Compute rookie's score";

    private $performance_based_attributes = RookieScoreEnum::PERFORMANCE_BASED_ATTRIBUTES;
    private $backend_performance_based_attributes = RookieScoreEnum::BACKEND_PERFORMANCE_BASED_ATTRIBUTES;
    private $under_3_subscriptions_attributes = ['avg_subscriptions_period', 'hungry_rookies', 'leaders_retaining_rookie', 'avg_gifts_amounts'];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $job_history = JobHistory::query()->create(['type' => JobHistoryEnum::ROOKIE_SCORE, 'start_at' => now(), 'end_at' => now()]);

        $rookies_score = RookieScore::query()->selectRaw('rookies_score.*')
            ->join('users', 'users.id', '=', 'rookies_score.rookie_id')
            ->where('users.active', true)
            ->whereDate('users.last_activity_at', '>=', now()->subMonth()->toDateString())
            ->distinct('rookies_score.rookie_id')
            ->get();

        $this->alert("FETCHING {$rookies_score->count()} USERS | " . now()->toDateTimeString());

        $attributes_matrix = $this->createAttributesMatrix();

        $rookies_ids = $rookies_score->pluck('rookie_id')->toArray();
        $rookies_stats = RookieStats::query()->whereIn('rookie_id', $rookies_ids)->get();

        foreach ($rookies_score as $rookie_score){

            $rookie_stats = $rookies_stats->where('rookie_id', $rookie_score->rookie_id)->last();
            if(!isset($rookie_stats)){
                continue;
            }

            $rookie_has_under_3_subscriptions = Subscription::query()
                    ->where('rookie_id', $rookie_score->rookie_id)
                    ->where('status', 'active')
                    ->count() < 3;

            $values_to_update = [
                'logins_streak' => ($rookie_stats->logins_streak>=10) ? 10 : $rookie_stats->logins_streak,
                'photos' => $rookie_stats->photos*2,
                'videos' => ($rookie_stats->videos>=1) ? 10 : 0,
                'has_description' => ($rookie_stats->has_description) ? 10 : 0,
                'joined_telegram_bot' => ($rookie_stats->joined_telegram_bot) ? 40 : 0,
                'leaders_referred' => ($rookie_stats->leaders_referred*10>=30) ? 30 : $rookie_stats->leaders_referred*10,
                'first_micromorgi_gift_leaders' => ($rookie_stats->first_micromorgi_gift_leaders*3>=30) ? 30 : $rookie_stats->first_micromorgi_gift_leaders*3,
                'leaders_first_subscription' => $rookie_stats->leaders_first_subscription*10,
                'morgi_last_seven_days' => Transaction::query()
                    ->where('rookie_id', $rookie_score->rookie_id)
                    ->whereIn('type', ['gift', 'bonus'])
                    ->whereNotNull('morgi')
                    ->whereNull('refund_type')
                    ->whereDate('created_at', '>=', Carbon::now()->subWeek())
                    ->sum('morgi'),
                'micromorgi_last_seven_days' => Transaction::query()
                    ->where('rookie_id', $rookie_score->rookie_id)
                    ->whereIn('type', ['chat', 'bonus'])
                    ->whereNotNull('micromorgi')
                    ->whereNull('refund_type')
                    ->whereDate('created_at', '>=', Carbon::now()->subWeek())
                    ->sum('micromorgi')
            ];

            $performance = array_merge(
                $this->performance_based_attributes,
                $this->backend_performance_based_attributes
            );

            foreach ($performance as $attribute){

                $is_reverse = (in_array($attribute, RookieScoreEnum::ATTRIBUTES_REVERSE_SCORE));
                $multiplier = RookieScoreEnum::ATTRIBUTES_MAX_SCORE[$attribute]/10;

                if($rookie_has_under_3_subscriptions && in_array($attribute, $this->under_3_subscriptions_attributes)){
                    $values_to_update[$attribute] = $multiplier * 6;
                    continue;
                }

                $values_to_update[$attribute] = $this->getScoreByMatrix($attributes_matrix[$attribute], $rookie_stats->$attribute, $is_reverse) * $multiplier;
            }

            $this->info("FETCHING $rookie_score->rookie_id | " . http_build_query($values_to_update));

            $rookie_score->update($values_to_update);
        }

        $this->info("FINISHED | " . now()->toDateTimeString());
        $job_history->update(['completed' => true, 'completed_at' => now()]);

        return 0;
    }


    private function getMaxScoreValues(){

        $max_values_attribute = RookieStats::query()
            ->whereDate('created_at', '>=', Carbon::now()->subHours(3))
            ->get();

        $max_values = [];

        $performance = array_merge(
            $this->performance_based_attributes,
            $this->backend_performance_based_attributes
        );

        foreach ($performance as $attribute){
            $max_values[$attribute] = $max_values_attribute->max($attribute);
        }

        return $max_values;
    }

    private function createAttributesMatrix(){

        $matrix = [];
        $max_values = $this->getMaxScoreValues();

        foreach ($max_values as $max_value_name => $max_value){
            $this->info("[MAX VALUE] {$max_value_name} => {$max_value}");
        }

        $performance = array_merge(
            $this->performance_based_attributes,
            $this->backend_performance_based_attributes
        );

        foreach ($performance as $attribute){

            $attribute_division_by = 10;
            $is_reverse = (in_array($attribute, RookieScoreEnum::ATTRIBUTES_REVERSE_SCORE));
            $value = $this->createMatrix($max_values[$attribute], $attribute_division_by, $is_reverse);

            if($attribute === 'converters_subscriptions'){
                $matrix[$attribute] = ($value >= 5) ? $value : 0;
                continue;
            }
            $matrix[$attribute] = $value;
        }

        return $matrix;
    }

    private function createMatrix($max_value, $division_by, $is_reverse){

        $matrix = [];

        for ($i=1; $i<=$division_by; $i++){
            $matrix[$i] = ($max_value/$division_by) * $i;
        }

        //Lets reverse values if is inverse
        if($is_reverse){
            $keys = array_keys($matrix);
            $values = array_reverse(array_values($matrix));
            $matrix = array_combine($keys, $values);
        }

        return $matrix;
    }

    private function getScoreByMatrix($matrix, $stat, $is_inverse){

        if($stat===0){
            return 0;
        }

        $matrix_count = count($matrix);

        if(array_key_exists($matrix_count, $matrix) && $matrix[$matrix_count]===0){
            return $matrix_count;
        }

        foreach ($matrix as $score => $value){

            if($is_inverse){
                if($stat>=$value){
                    return $score;
                }
            }else if($stat<=$value){
                return $score;
            }

        }

        return $matrix_count;
    }
}
