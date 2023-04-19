<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RookieOfTheDay extends Model
{
    use HasFactory;

    protected $table = 'rookies_of_the_days';

    protected $fillable = [
        'rookie_id',
        'morgi',
        'micro_morgi',
        'score',
        'max_score'
    ];
}
