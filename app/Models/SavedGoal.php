<?php

namespace App\Models;

use App\Models\Goal;
use App\Models\Leader;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SavedGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'goal_id',
        'leader_id'
    ];

    public function goal()
    {
        return $this->belongsTo(Goal::class,'goal_id');
    }

    public function leader()
    {
        return $this->belongsTo(Leader::class,'leader_id');
    }
}
