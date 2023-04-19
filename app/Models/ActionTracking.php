<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActionTracking extends Model
{
    use HasFactory;

    protected $table = "actions_tracking";

    protected $fillable = [
        'leader_id',
        'rookie_id',
        'clicked_profile',
        'saw_video',
        'saved_profile',
        'paid_rookie',
        'time_in_rookie_profile_in_seconds',
        'shared_profile'
    ];
}
