<?php

namespace App\Models;

use App\Utils\StorageUtils;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoalMedia extends Model
{
    use HasFactory;

    const VALID_EXTENSIONS = [
        self::TYPE_IMAGE => ['jpeg', 'jpg', 'png', 'heif', 'heic'],
        self::TYPE_AVATAR => ['jpeg', 'jpg', 'png', 'heif', 'heic'],
        self::TYPE_HEADER => ['jpeg', 'jpg', 'png', 'heif', 'heic'],
        self::TYPE_VIDEO => ['mp4', 'mov', 'qt']
    ];

    const FILE_TYPES = [
        self::TYPE_IMAGE => 'image',
        self::TYPE_AVATAR => 'image',
        self::TYPE_HEADER => 'image',
        self::TYPE_VIDEO => 'video',
    ];

    const TYPE_IMAGE = "image";
    const TYPE_VIDEO = "video";
    const TYPE_AVATAR = "avatar";
    const TYPE_HEADER = "header";

    const TYPES = [
        self::TYPE_IMAGE,
        self::TYPE_VIDEO,
        self::TYPE_AVATAR,
        self::TYPE_HEADER
    ];

    protected $fillable = [
        'path_location', 'type' ,'goal_id'
    ];

    protected $appends = [
        'url'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function getUrlAttribute()
    {
        return StorageUtils::signUrl($this->path_location);
    }
}
