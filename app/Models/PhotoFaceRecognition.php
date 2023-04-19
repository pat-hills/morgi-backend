<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotoFaceRecognition extends Model
{
    use HasFactory;

    protected $table = 'photos_face_recognition';

    protected $fillable = [
        'photo_id',
        'confidence',
        'external_image_id',
        'payload',
        'collection_id'
    ];
}
