<?php

namespace App\Models;

use App\Enums\NotificationTypeEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'notification_type_id',
        'ref_user_id',
        'seen_at',
        'amount_micromorgi',
        'event_at',
        'currency',
        'amount_morgi',
        'amount',
        'old_amount',
        'reason',
        'new_username',
        'new_birth_date',
        'goal_id'
    ];

    public function getRefUsernameAttribute()
    {
        if(!isset($this->ref_user_id)){
            return null;
        }

        $user = User::find($this->ref_user_id);
        if(!isset($user)){
            return null;
        }

        return $user->username;
    }

    public function getEventAtAttribute($value)
    {
        return Carbon::createFromDate($value)->format('Y-m-d');
    }

    public function mapDescription($notification_type)
    {
        if(!$notification_type){
            return null;
        }

        $tags_to_search = NotificationTypeEnum::TYPES_TAGS[$notification_type->type];
        $tags_to_replace = NotificationTypeEnum::TYPES_TAGS_ATTRIBUTE[$notification_type->type];

        foreach ($tags_to_replace as $key=>$tag){
            $tags_to_replace[$key] = $this->adaptTagText($key, $this->$tag);
        }

        return str_replace($tags_to_search, $tags_to_replace, $notification_type->content);
    }

    private function adaptTagText($tag_key, $tag_value): ?string
    {
        return ($tag_key === 'reason')
            ? lcfirst($tag_value)
            : $tag_value;
    }

    public function getGoalNameAttribute(): ?string
    {
        if(!isset($this->goal_id)){
            return null;
        }

        $goal = Goal::find($this->goal_id);
        if(!isset($goal)){
            return null;
        }

        return $goal->name;
    }

    public function getGoalEndDateAttribute(): ?string
    {
        if(!isset($this->goal_id)){
            return null;
        }

        $goal = Goal::find($this->goal_id);
        if(!isset($goal)){
            return null;
        }

        return Carbon::create($goal->end_date)->toDateString();
    }
}
