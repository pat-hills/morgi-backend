<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RookieSeen extends Model
{
    use HasFactory;

    protected $table = 'rookies_seen';

    protected $fillable = [
        'leader_id',
        'rookie_id',
        'source',
        'leader_type',
        'session_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
