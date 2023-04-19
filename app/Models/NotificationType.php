<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationType extends Model
{
    use HasFactory;

    protected $table = 'notifications_types';

    protected $fillable = [
        'type',
        'content',
        'user_type',
        'title'
    ];
}
