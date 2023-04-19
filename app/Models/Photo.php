<?php

namespace App\Models;

use App\Utils\StorageUtils;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Photo extends Model
{
    use HasFactory;

    protected $table = 'photos';

    protected $fillable = [
        'user_id',
        'path_location',
        'main',
        'title',
        'content',
        'tags',
        'is_face_recognition_processed'
    ];

    protected $appends = [
        'url'
    ];

    protected $casts = [
        'main' => 'boolean',
        'is_face_recognition_processed' => 'boolean'
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
