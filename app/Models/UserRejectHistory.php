<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRejectHistory extends Model
{
    use HasFactory;

    protected $table = 'users_rejected_histories';

    protected $fillable = [
        'user_id',
        'admin_id',
        'reason'
    ];
}
