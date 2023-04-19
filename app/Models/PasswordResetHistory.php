<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetHistory extends Model
{
    use HasFactory;

    protected $table = 'password_resets_histories';

    protected $fillable = [
        'user_id',
        'email',
        'ip_address'
    ];
}
