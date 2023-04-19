<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PubnubChannelSetting extends Model
{
    use HasFactory;

    protected $table = 'pubnub_channels_settings';

    protected $fillable = [
        'type',
        'is_active'
    ];
}
