<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;


class ChatTopic extends Model
{
    use HasFactory;

    protected $table = 'chat_topics';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'key_name'
    ];


    public function usersChatTopics()
    {
        return $this->belongsToMany(User::class, 'chat_topics_users', 'chat_topics_id', 'users_id');
    }


}
