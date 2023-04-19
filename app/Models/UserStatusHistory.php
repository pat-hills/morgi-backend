<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'users_statues_histories';

    protected $fillable = [
        'user_id',
        'old_status',
        'new_status',
        'changed_by',
        'reason'
    ];
}
