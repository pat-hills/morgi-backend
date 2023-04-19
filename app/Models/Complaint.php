<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_reported',
        'reported_by',
        'type_id',
        'by_system',
        'status',
        'notes',
        'follow_up',
        'message',
        'counter_follow_up',
        'created_at',
        'updated_at'
    ];

    protected $appends = [
        'reported_content',
        'on_user'
    ];

    public function getReportedContentAttribute()
    {
        $content = json_decode($this->message, true);
        if(!isset($content)){
            return ['type' => 'error', 'content' => 'Reported message/video/photo not found'];
        }

        if(in_array($content['type'], ['video', 'image', 'photo'])){
            $attachment = ChatAttachment::query()->find($content['meta']['attachmentId']);
            return ['type' => $content['type'], 'content' => $attachment->url];
        }

        return ['type' => $content['type'], 'content' => $content['text'] ?? null];
    }

    public function getOnUserAttribute()
    {
        return User::query()->find($this->user_reported);
    }
}
