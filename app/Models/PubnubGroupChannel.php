<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PubnubGroupChannel extends Model
{
    use HasFactory;

    protected $table = 'pubnub_groups_channels';

    protected $fillable = [
        'channel_id',
        'group_id'
    ];
}
