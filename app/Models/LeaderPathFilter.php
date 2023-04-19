<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaderPathFilter extends Model
{
    use HasFactory;

    protected $table = "leaders_paths_filters";

    protected $fillable = [
        'leader_id',
        'path_id'
    ];
}
