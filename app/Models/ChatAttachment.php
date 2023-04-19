<?php

namespace App\Models;

use App\Utils\StorageUtils;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'type',
        'path_location',
        'title'
    ];

    protected $appends = [
        'url'
    ];

    protected $hidden = [
        'updated_at', 'title'
    ];

    public function getUrlAttribute()
    {
        return StorageUtils::signUrl($this->path_location);
    }

    public function canViewAttachment(int $user_id)
    {
        return in_array($user_id, [$this->receiver_id, $this->sender_id], true);
    }
}
