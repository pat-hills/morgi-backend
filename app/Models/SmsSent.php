<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsSent extends Model
{
    use HasFactory;

    protected $table = 'sms_sent';

    protected $fillable = [
        'user_id',
        'telephone',
        'is_sent',
        'error',
        'message'
    ];

    protected $casts = [
        'is_sent' => 'boolean'
    ];
}
