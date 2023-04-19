<?php

namespace App\Models;

use App\Utils\StorageUtils;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoalProof extends Model
{
    use HasFactory;
    public const TYPE_VIDEO = 'video';
    public const TYPE_IMAGE = 'image';

    const FILE_TYPES = [
        self::TYPE_IMAGE => 'image',
        self::TYPE_VIDEO => 'video',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_DECLINED = 'declined';
    public const STATUS_APPROVED = 'approved';

    protected $fillable = [
        'goal_id', 'type', 'path_location', 'status', 'declined_reason', 'admin_id'
    ];

    protected $appends = [
        'url'
    ];

    public function getUrlAttribute()
    {
        return StorageUtils::signUrl($this->path_location);
    }
}
