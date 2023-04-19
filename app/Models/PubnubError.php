<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PubnubError extends Model
{
    use HasFactory;

    protected $fillable = [
        'users',
        'channels',
        'channels_groups',
        'status_code',
        'message',
        'api_name'
    ];
}
