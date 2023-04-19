<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDescriptionHistory extends Model
{
    use HasFactory;

    protected $table = 'users_descriptions_histories';

    protected $fillable = [
        'user_id',
        'description',
        'status',
        'decline_reason',
        'admin_id'
    ];
}
