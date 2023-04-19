<?php

namespace App\Models;

use App\Utils\StorageUtils;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
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
        'deleted_at',
        'updated_at'
    ];

    protected $appends = [
        'url'
    ];

    public function getUnderValidationAttribute()
    {
        return false;
    }

    public function getUrlAttribute()
    {
        return env('AWS_URL') . $this->path_location;
    }
}
