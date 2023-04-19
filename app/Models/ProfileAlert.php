<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileAlert extends Model
{
    use HasFactory;

    protected $table = 'profiles_alerts';

    protected $fillable = [
        'user_id',
        'code_id',
        'seen_at'
    ];
}
