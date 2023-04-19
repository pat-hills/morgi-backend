<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivityHistory extends Model
{
    use HasFactory;

    protected $table = 'users_activities_history';

    protected $fillable = [
        'user_id'
    ];
}
