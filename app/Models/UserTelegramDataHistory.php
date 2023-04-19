<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTelegramDataHistory extends Model
{
    use HasFactory;

    protected $table = 'users_telegram_data_history';

    protected $fillable = [
        'user_id',
        'telegram_username',
        'telegram_user_id',
        'telegram_chat_id'
    ];
}
