<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEmailBlacklist extends Model
{
    use HasFactory;

    protected $table = 'users_emails_blacklist';

    protected $fillable = [
        'email'
    ];
}
