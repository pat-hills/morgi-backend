<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaderFaceRecognitionMatch extends Model
{
    use HasFactory;

    protected $table = "leaders_face_recognition_matches";

    protected $fillable = [
        'leader_id',
        'rookie_id',
        'leader_photo_id',
        'rookie_photo_id'
    ];
}
