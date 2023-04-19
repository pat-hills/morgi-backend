<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserBlock extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'users_blocks';

    protected $fillable = [
        'from_user_id',
        'to_user_id'
    ];
}
