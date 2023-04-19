<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Broadcast extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'is_goal',
        'display_name'
    ];

    protected $casts = [
        "is_goal" => "boolean"
    ];

    public function sender() {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function messages() {
        return $this->hasMany(BroadcastMessage::class)->orderBy('broadcast_messages.id', 'desc') ;
    }

    public function goals() {
        return $this->belongsToMany(Goal::class, 'broadcast_goals', 'broadcast_id', 'goal_id');
    }

    public function users() {
        return $this->belongsToMany(User::class, 'broadcast_users', 'broadcast_id', 'user_id');
    }

    public function getImageLinkAttribute() {

        if($this->is_goal){

            $goal = $this->goals()->first();
            if(!isset($goal)){
                return null;
            }

            $media = $goal->media()->first();

            return $media !== null
                ? $media->url
                : null;
        }

        return env("BROADCAST_PLACEHOLDER_URL", "");
    }
}
