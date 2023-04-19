<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserABGroup extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'users_ab_groups';

    protected $fillable = [
        'name'
    ];
}
