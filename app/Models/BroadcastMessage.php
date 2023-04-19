<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BroadcastMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
        'broadcast_id'
    ];

    //TODO remove when we define better broadcast responses
    protected $with = ['attachment', 'teaser'];

    public const METADATA_TYPE_DOTTED = 'dotted'; // To distinguish system communications and such
    public const METADATA_TYPE_GOAL = 'goal'; // Goal widget

    public function broadcast(){
        return $this->belongsTo(Broadcast::class);
    }

    public function attachment(){
        return $this->hasOne(BroadcastAttachment::class);
    }

    public function teaser(){
        return $this->hasOne(BroadcastTeaser::class);
    }
}
