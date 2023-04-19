<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BroadcastTeaser extends Model
{
    use HasFactory;

    protected $fillable = [
        'goal_id',
        'broadcast_message_id',
        'type'
    ];

    public function message(){
        return $this->belongsTo(BroadcastMessage::class, 'broadcast_message_id');
    }

    public function goal(){
        return $this->belongsTo(Goal::class, 'goal_id');
    }
}
