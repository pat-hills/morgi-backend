<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RookieStats extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'rookies_stats';

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
        'avg_gifts_amounts_in_dollars',
        'leaders_retaining_rookie',
        'hungry_rookies',
        'leaders_first_subscription',
        'converters_subscriptions',
        'time_to_subscribe'
    ];

    public static function multiInsert(array $data): void
    {
        if(empty($data)){
            return;
        }

        $batch_per_insert = 500;
        $counter = 1;
        $batch_counter = 0;
        $batches = [];

        // Create batches
        foreach ($data as $row){
            $batches[$batch_counter][] = $row;
            $counter++;

            if($counter >= $batch_per_insert){
                $batch_counter++;
                $counter = 1;
            }
        }

        // Insert batches
        foreach ($batches as $batch){
            self::insert($batch);
        }
    }
}
