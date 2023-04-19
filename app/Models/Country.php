<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'alpha_3_code',
        'dial',
        'has_childs',
        'currency',
        'alpha_2_code',
        'timezone'
    ];
}
