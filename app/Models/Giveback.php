<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Giveback extends Model
{
    use HasFactory;

    protected $table = 'givebacks';

    protected $fillable = [
        'total_subscriptions_count',
        'micromorgi'
    ];
}
