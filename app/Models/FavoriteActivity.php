<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavoriteActivity extends Model
{
    use HasFactory;

    protected $table = 'favorite_activities';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'key_name'
    ];

    public function usersFavoriteActivities()
    {
        return $this->belongsToMany(User::class, 'favorite_activities_users', 'favorite_activities_id', 'users_id');
    }
}
