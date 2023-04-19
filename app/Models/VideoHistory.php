<?php

namespace App\Models;

use App\Utils\StorageUtils;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoHistory extends Model
{
    use HasFactory;

    protected $table = 'videos_histories';

    protected $fillable = [
        'user_id',
        'admin_id',
        'status',
        'decline_reason',
        'path_location',
        'title',
        'content',
        'tags',
        'is_processed'
    ];

    protected $hidden = [
        'title',
        'content',
        'tags',
        'user_id',
        'created_at',
        'updated_at',
        'admin_id',
        'status'
    ];

    protected $appends = [
        'url'
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
