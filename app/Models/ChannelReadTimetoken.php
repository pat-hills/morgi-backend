<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChannelReadTimetoken extends Model
{
    use HasFactory;

    protected $table = 'channels_reads_timetokens';

    protected $fillable = [
        'user_id',
        'channel_id',
        'timetoken'
    ];
}
