<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConverterCarouselPosition extends Model
{
    use HasFactory;

    protected $table = 'converters_carousel_positions';

    protected $fillable = [
        'position'
    ];
}
