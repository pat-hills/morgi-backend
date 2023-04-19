<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CcbillPayload extends Model
{
    use HasFactory;

    protected $fillable = [
        'payload',
        'workflow_completed',
        'error',
        'uuid'
    ];
}
