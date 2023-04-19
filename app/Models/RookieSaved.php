<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RookieSaved extends Model
{
    use HasFactory;

    protected $table = 'rookies_saved';

    protected $fillable = [
        'leader_id',
        'rookie_id',
        'photo_id'
    ];

    protected $hidden = [
        'id',
        'updated_at',
        'leader_id',
        'rookie_id'
    ];

    public function getRookieAttribute()
    {
        $user = User::query()->find($this->rookie_id);
        return [
            'id' => $user->id,
            'full_name' => $user->full_name,
            'avatar' => $user->getPublicAvatar(),
            'username' => $user->username
        ];
    }
}
