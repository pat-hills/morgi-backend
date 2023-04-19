<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoalType extends Model
{
    use HasFactory;

    use HasFactory;

    public const SMALL_SIZE_GOAL = 'small_size_goal';
    public const MEDIUM_SIZE_GOAL = 'medium_size_goal';
    public const LARGE_SIZE_GOAL = 'large_size_goal';

    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'min' => 'int',
        'max' => 'int',
    ];

    protected $fillable = [
        'type', 'min', 'max', 'duration_type', 'duration_value'
    ];

    public function scopeWhereIsActive($query)
    {
        return $query->where('is_active', true);
    }
}
