<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPath extends Model
{
    use HasFactory;

    protected $table = 'users_paths';

    protected $fillable = [
        'user_id',
        'path_id',
        'is_subpath'
    ];

    protected $appends = [
        'path'
    ];

    protected $hidden = [
        'path_id'
    ];

    protected $casts = [
        'is_subpath' => 'boolean'
    ];
}
