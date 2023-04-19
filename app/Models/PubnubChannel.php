<?php

namespace App\Models;

use App\Enums\PubnubGroupEnum;
use App\Services\Chat\Utils;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PubnubChannel extends Model
{
    use HasFactory;

    protected $table = 'pubnub_channels';

    protected $fillable = [
        'type',
        'display_name',
        'name',
        'category',
        'active',
        'rookie_id',
        'leader_id',
        'subscription_id',
        'users_referral_emails_sent_id',
        'created',
        'user_block_id',
        'is_referral',
        'leader_first_message_at',
        'rookie_first_message_at',
        'avg_response_time',
        'is_paused',
        'time_to_subscribe',
        'was_ever_paused',
        'last_activity_at',
        'channel_setting_id',
        'leader_received_ping_email_at',
        'goal_id'
    ];

    protected $appends = [
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
        'created' => 'boolean',
        'is_referral' => 'boolean',
        'is_paused' => 'boolean',
        'was_ever_paused' => 'boolean',
    ];

    public function scopeSearch(Builder $query, int $leader_id, int $rookie_id): Builder
    {
        return $query->where('leader_id', $leader_id)->where('rookie_id', $rookie_id);
    }

    public function getActiveAttribute($value): ?bool
    {
        if($this->is_paused){
            return false;
        }

        if(!isset($this->subscription_id)){
            return $value;
        }

        $subscription = Subscription::find($this->subscription_id);
        if(!isset($subscription)) {
            return $value;
        }

        if(isset($subscription->deleted_at)){
            return false;
        }

        if($subscription->status === 'failed' || isset($subscription->user_block_id)){
            return false;
        }

        if($subscription->status === 'active'){
            return true;
        }

        return strtotime($subscription->valid_until_at) >= now()->timestamp;
    }

    public function getLeaderAwaitingReplyAttribute(): bool
    {
        return isset($this->leader_first_message_at) && !isset($this->rookie_first_message_at);
    }

    public function getLastMessageAtAttribute()
    {
        $last_message = PubnubMessage::query()->where('channel_id', $this->id)
            ->latest('sent_at')
            ->first();

        return (isset($last_message)) ? $last_message->sent_at : null;
    }

    public function getMessagesCountAttribute(): int
    {
        return PubnubMessage::query()->where('channel_id', $this->id)->count();
    }

    public function getUnreadMessagesCount(int $user_id): ?int
    {
        $last_read = ChannelReadTimetoken::query()
            ->where('channel_id', $this->id)
            ->where('user_id', $user_id)
            ->latest()
            ->first();

        if(!isset($last_read)){
            return null;
        }

        $last_read_at = Carbon::createFromTimestamp(
            Utils::getTimestampFromTimetoken($last_read->timetoken)
        )->toDateTimeString();

        return PubnubMessage::query()
            ->where('channel_id', $this->id)
            ->where('sender_id', '!=', $user_id)
            ->where('sent_at', '>', $last_read_at)
            ->count();
    }

    public static function getChannelNameByCategory(string $category, int $leader_id, int $rookie_id): string
    {
        switch ($category){
            case PubnubGroupEnum::DIRECT_CATEGORY:
            default:
                $channel_name = $leader_id . '-' . $rookie_id;
                break;
        }

        return $channel_name;
    }
}
