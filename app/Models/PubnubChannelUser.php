<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PubnubChannelUser extends Model
{
    use HasFactory;

    protected $table = 'pubnub_channels_users';

    public $timestamps = false;

    protected $fillable = [
        'channel_id',
        'user_id'
    ];
}
