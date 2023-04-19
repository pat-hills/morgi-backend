<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrazioSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'leader_id',
        'session',
        'reason',
        'leader_type'
    ];
}
