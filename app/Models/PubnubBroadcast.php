<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PubnubBroadcast extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_id',
        'leader_id',
        'rookie_id',
        'transaction_id',
        'type',
        'message_id'
    ];
}
