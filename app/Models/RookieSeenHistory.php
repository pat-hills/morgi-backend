<?php

namespace App\Models;

use App\Http\Resources\UserResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class RookieSeenHistory extends Model
{
    use HasFactory;

    protected $table = 'rookies_seen_histories';

    protected $fillable = [
        'leader_id',
        'rookie_id',
        'photo_id',
        'seen_at',
        'source',
        'leader_type',
        'session_id'
    ];
}
