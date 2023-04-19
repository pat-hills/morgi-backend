<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalGroup extends Model
{
    use HasFactory;

    protected $table = 'globals_groups';

    protected $fillable = [
        'global_id'
    ];
}
