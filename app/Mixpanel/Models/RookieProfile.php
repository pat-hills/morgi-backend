<?php

namespace App\Mixpanel\Models;

use App\Models\City;
use App\Models\Path;
use App\Models\PaymentRookie;
use App\Models\Photo;
use App\Models\PubnubChannel;
use App\Models\Region;
use App\Models\Rookie;
use App\Models\RookieScore;
use App\Models\RookieStats;
use App\Models\User;
use App\Models\UserBlock;
use App\Models\Video;
use Carbon\Carbon;

class RookieProfile extends UserProfile
{
    protected $rookie;
    public $first_name;
    public $last_name;
    public $age;
    public $has_video;
    public $rookie_path;
    public $rookie_sub_path;
    public $profile_score;
    public $morgi_balance;
    public $micromorgi_balance;
    public $referred_leaders_count;
    public $blocked_leaders;
    public $beauty_score;
    public $is_converter;
    public $ever_got_paid;
    public $city;
    public $region;
    public $average_first_reply_time_seconds;
    public $total_connections_replied; // Every connection with at least one message from the rookie
    public $total_photos_count;
    public $rookie_state; // Activity state

    public function __construct(int $user_id)
    {
        parent::__construct($user_id);

        $this->rookie = Rookie::find($user_id);
        $this->first_name = $this->rookie->first_name;
        $this->last_name = $this->rookie->last_name;
        $this->age = Carbon::create($this->rookie->birth_date)->age;
        $this->has_video = Video::query()->where('user_id', $this->id)->exists();
        $this->rookie_path = $this->computeRookiePath();
        $this->rookie_sub_path = $this->computeRookieSubPath();
        $this->profile_score = $this->computeProfileScore();
        $this->morgi_balance = $this->rookie->morgi_balance;
        $this->micromorgi_balance = $this->rookie->untaxed_micro_morgi_balance;
        $this->referred_leaders_count = $this->computeReferredLeadersCount();
        $this->blocked_leaders = $this->computeBlockedLeaders();
        $this->beauty_score = $this->rookie->beauty_score;
        $this->is_converter = $this->rookie->is_converter;
        $this->ever_got_paid = $this->computeEverGotPaid();
        $this->city = $this->computeCity();
        $this->region = $this->computeRegion();
        $this->average_first_reply_time_seconds = $this->computeAverageFirstReplyTimeSeconds();
        $this->total_connections_replied = $this->computeTotalConnectionsReplied();
        $this->total_photos_count = $this->computeTotalPhotosCount();
        $this->rookie_state = $this->computeRookieState();
    }

    public function toArray(): array
    {
        $user = parent::toArray();
        $rookie = [
            '$first_name' => $this->first_name,
            '$last_name' => $this->last_name,
            'Age' => $this->age,
            'Has video?' => $this->has_video,
            'Rookie path' => $this->rookie_path,
            'Rookie subpath' => $this->rookie_sub_path,
            'Profile score' => $this->profile_score,
            'Morgi balance' => $this->morgi_balance,
            'Micromorgi balance' => $this->micromorgi_balance,
            'Referred leaders count' => $this->referred_leaders_count,
            'Blocked leaders' => $this->blocked_leaders,
            'Beauty score' => $this->beauty_score,
            'Is converter?' => $this->is_converter,
            'Ever got paid?' => $this->ever_got_paid,
            'System city' => $this->city,
            'Region' => $this->region,
            'Avg first reply time in seconds' => $this->average_first_reply_time_seconds,
            'Total connections replied' => $this->total_connections_replied,
            'Photos count' => $this->total_photos_count,
            'Rookie state' => $this->rookie_state
        ];

        return array_merge($user, $rookie);
    }

    private function computeTotalPhotosCount(): int
    {
        return Photo::query()->where('user_id', $this->id)->count();
    }

    private function computeRookiePath(): string
    {
        $path = Path::query()->selectRaw('paths.*')
            ->join('users_paths', 'users_paths.path_id', '=', 'paths.id')
            ->where('users_paths.user_id', $this->id)
            ->where('paths.is_subpath', false)
            ->first();

        return (isset($path)) ? $path->name : 'None';
    }

    private function computeRookieSubPath(): string
    {
        $path = Path::query()->selectRaw('paths.*')
            ->join('users_paths', 'users_paths.path_id', '=', 'paths.id')
            ->where('users_paths.user_id', $this->id)
            ->where('paths.is_subpath', true)
            ->first();

        return (isset($path)) ? $path->name : 'None';
    }

    private function computeProfileScore(): int
    {
        $rookie_score = RookieScore::where('rookie_id', $this->id)->first();
        if(!isset($rookie_score)){
            return 0;
        }

        $performance = $rookie_score->getPerformanceBasedData();
        $action = $rookie_score->getActionBasedData();

        return $performance['score'] + $action['score'];
    }

    private function computeReferredLeadersCount(): int
    {
        return User::query()->where('referred_by', $this->id)
            ->where('type', 'leader')
            ->count();
    }

    private function computeBlockedLeaders(): int
    {
        return UserBlock::query()->where('from_user_id', $this->id)->count();
    }

    private function computeEverGotPaid(): bool
    {
        return PaymentRookie::query()->where('rookie_id', $this->id)
            ->where('status', 'successful')
            ->exists();
    }

    private function computeCity(): string
    {
        $city = City::find($this->rookie->country_id);
        return (isset($city)) ? $city->name : 'None';
    }

    private function computeRegion(): string
    {
        if(isset($this->rookie->region_name)){
            return $this->rookie->region_name;
        }

        $region = Region::find($this->rookie->region_id);
        return (isset($region)) ? $region->name : 'None';
    }

    private function computeTotalConnectionsReplied(): int
    {
        return PubnubChannel::query()->selectRaw('pubnub_channels.*')
            ->join('pubnub_messages', 'pubnub_messages.channel_id', '=', 'pubnub_channels.id')
            ->where('pubnub_channels.rookie_id', $this->id)
            ->where('pubnub_messages.sender_id', $this->id)
            ->count();
    }

    private function computeAverageFirstReplyTimeSeconds(): int
    {
        $rookie_stats = RookieStats::query()
            ->where('rookie_id', $this->id)
            ->latest()
            ->first();

        return (isset($rookie_stats)) ? $rookie_stats->avg_first_response_time_seconds : 0;
    }

    private function computeRookieState(): string
    {
        $is_active_past_ten_days = strtotime($this->user->last_activity_at) > now()->subDays(10)->timestamp;
        if(!$is_active_past_ten_days){
            return 'Fallback';
        }

        if($this->has_telegram_bot){
            return 'Active past 10 days + connected to bot';
        }

        return 'Active past 10 days';
    }
}
