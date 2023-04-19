<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PubnubMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'sender_id',
        'receiver_id',
        'channel_id',
        'sent_at'
    ];
}
