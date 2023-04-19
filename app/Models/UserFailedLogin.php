<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFailedLogin extends Model
{
    use HasFactory;

    protected $table = 'users_failed_logins';

    protected $fillable = [
        'user_id',
        'password_forced'
    ];

    protected $casts = [
        'password_forced'
    ];
}
