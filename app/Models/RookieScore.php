<?php

namespace App\Models;

use App\Enums\RookieScoreEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RookieScore extends Model
{
    use HasFactory;

    protected $table = 'rookies_score';

    protected $fillable = [
        'rookie_id',
        'logins_streak',
        'photos',
        'videos',
        'has_description',
        'joined_telegram_bot',
        'first_micromorgi_gift_leaders',
        'leaders_referred',
        'active_subscriptions',
        'leaders_sending_micromorgi_last_seven_days',
        'avg_subscriptions_period',
        'avg_first_response_time_seconds',
        'morgi_last_seven_days',
        'micromorgi_last_seven_days',
        'avg_gifts_amounts',
        'leaders_retaining_rookie',
        'hungry_rookies',
        'leaders_first_subscription',
        'converters_subscriptions',
        'time_to_subscribe'
    ];

    public static function getAttributesMaxScore()
    {
        $total_max_score = 0;
        $action_based_max_score = 0;
        $performance_based_max_score = 0;

        foreach (RookieScoreEnum::ACTION_BASED_ATTRIBUTES as $attribute){

            if($attribute === 'leaders_first_subscription'){
                continue;
            }

            $max_score = RookieScoreEnum::ATTRIBUTES_MAX_SCORE[$attribute];
            $total_max_score += $max_score;
            $action_based_max_score += $max_score;
        }

        foreach (RookieScoreEnum::PERFORMANCE_BASED_ATTRIBUTES as $attribute){
            $max_score = RookieScoreEnum::ATTRIBUTES_MAX_SCORE[$attribute];
            $total_max_score += $max_score;
            $performance_based_max_score += $max_score;
        }

        return [
            'total_max_score' => $total_max_score,
            'action_based_max_score' => $action_based_max_score,
            'performance_based_max_score' => $performance_based_max_score
        ];
    }

    public function getPerformanceBasedData()
    {
        $score = 0;
        $data = [];

        $rookie_stats = RookieStats::query()
            ->where('rookie_id', $this->rookie_id)
            ->latest()
            ->first();

        foreach (RookieScoreEnum::PERFORMANCE_BASED_ATTRIBUTES as $attribute){

            $score += $this->$attribute;
            $time = (isset($rookie_stats)) ? $rookie_stats->$attribute : 0;

            $data[$attribute] = [
                'points' => $this->$attribute,
                'max' => RookieScoreEnum::ATTRIBUTES_MAX_SCORE[$attribute],
                'label' => trans("rookie_score.{$attribute}_label"),
                'description' => trans("rookie_score.{$attribute}_description"),
                'times' => $time,
                'completed' => RookieScoreEnum::ATTRIBUTES_MAX_SCORE[$attribute] === $this->$attribute
            ];
        }

        return [
            'score' => $score,
            'data' => $data
        ];
    }

    public function getActionBasedData()
    {
        $score = 0;
        $data = [];

        $rookie_stats = RookieStats::query()
            ->where('rookie_id', $this->rookie_id)
            ->latest()
            ->first();

        foreach (RookieScoreEnum::ACTION_BASED_ATTRIBUTES as $attribute){

            if($attribute === 'leaders_first_subscription'){
                continue;
            }

            $score += $this->$attribute;
            $time =  (isset($rookie_stats)) ? $rookie_stats->$attribute : 0;

            $data[$attribute] = [
                'points' => $this->$attribute,
                'max' => RookieScoreEnum::ATTRIBUTES_MAX_SCORE[$attribute],
                'label' => trans("rookie_score.{$attribute}_label"),
                'description' => trans("rookie_score.{$attribute}_description"),
                'times' => $time,
                'completed' => RookieScoreEnum::ATTRIBUTES_MAX_SCORE[$attribute] === $this->$attribute
            ];
        }

        return [
            'score' => $score,
            'data' => $data
        ];
    }
}
