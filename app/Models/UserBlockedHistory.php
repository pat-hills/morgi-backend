<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBlockedHistory extends Model
{
    use HasFactory;

    protected $table = 'users_blocked_histories';

    protected $fillable = [
        'user_id',
        'admin_id',
        'reason',
        'created_at'
    ];
}
