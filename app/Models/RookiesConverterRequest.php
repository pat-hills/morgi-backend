<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RookiesConverterRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'rookie_id',
        'message'
    ];
}
