<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaderPath extends Model
{
    use HasFactory;

    protected $table = "leaders_paths";

    protected $fillable = [
        'source',
        'leader_id',
        'path_id',
        'is_main'
    ];
}
