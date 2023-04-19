<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RookieFaceRecognitionMatch extends Model
{
    use HasFactory;

    protected $table = "rookies_face_recognition_matches";

    protected $fillable = [
        'rookie_id',
        'to_rookie_id',
        'rookie_photo_id',
        'to_rookie_photo_id'
    ];
}
