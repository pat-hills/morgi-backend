<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintType extends Model
{
    use HasFactory;

    protected $table = "complaints_types";

    protected $fillable = [
        'name',
        'key_name'
    ];
}
