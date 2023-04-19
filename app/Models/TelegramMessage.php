<?php

namespace App\Models;

use App\Enums\TelegramMessageEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramMessage extends Model
{
    use HasFactory;

    protected $table = 'telegram_messages';

    protected $fillable = [
        'type',
        'message',
        'order',
        'media_type'
    ];

    public function getMessageAttribute($value)
    {
        if($this->media_type === TelegramMessageEnum::GIF){
            return env('AWS_URL') . $value;
        }

        return $value;
    }
}
