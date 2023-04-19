<?php

namespace App\Models;

use App\Utils\StorageUtils;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotoHistory extends Model
{
    use HasFactory;

    protected $table = 'photos_histories';

    protected $fillable = [
        'id',
        'user_id',
        'admin_id',
        'status',
        'path_location',
        'decline_reason',
        'main',
        'content',
        'tags',
        'title'
    ];

    protected $casts = [
        'main' => 'boolean'
    ];

    public function getUnderValidationAttribute()
    {
        return true;
    }

    public function getUrlAttribute()
    {
        return env('AWS_URL') . $this->path_location;
    }
}
