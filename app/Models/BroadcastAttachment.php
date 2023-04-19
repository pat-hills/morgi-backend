<?php

namespace App\Models;

use App\Utils\StorageUtils;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BroadcastAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'broadcast_message_id',
        'type'
    ];

    public function message()
    {
        return $this->belongsTo(BroadcastMessage::class, 'broadcast_message_id');
    }

    public function getUrlAttribute($value)
    {
        return StorageUtils::signUrl($value);
    }
}
