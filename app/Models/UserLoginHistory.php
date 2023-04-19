<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLoginHistory extends Model
{
    use HasFactory;

    protected $table = 'users_login_histories';

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'is_signup_values'
    ];

    protected $casts = [
        'is_signup_values' => 'boolean'
    ];
}
