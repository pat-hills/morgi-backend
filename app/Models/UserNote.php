<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNote extends Model
{
    use HasFactory;

    protected $table = 'users_notes';

    protected $fillable = [
        'user_id',
        'admin_id',
        'note',
        'created_at',
    ];
}
