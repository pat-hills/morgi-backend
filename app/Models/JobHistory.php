<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobHistory extends Model
{
    use HasFactory;

    protected $table = "jobs_history";

    protected $fillable = [
        'type',
        'start_at',
        'end_at',
        'completed',
        'completed_at'
    ];
}
