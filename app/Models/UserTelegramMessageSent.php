<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTelegramMessageSent extends Model
{
    use HasFactory;

    protected $table = 'users_telegram_messages_sent';

    protected $fillable = [
        'user_id',
        'message',
        'telegram_chat_id',
        'unread_messages',
        'type'
    ];
}
