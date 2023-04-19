<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaceRecognitionCollection extends Model
{
    use HasFactory;

    protected $table = 'face_recognition_collections';

    protected $fillable = [
        'type', 'name', 'is_active', 'is_full', 'aws_arn'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_full' => 'boolean'
    ];

    public function makeInactive(): void
    {
        $this->update(['is_active' => false]);
    }

    public function makeActive(): void
    {
        $this->update(['is_active' => true]);
    }

    public function makeFull(): void
    {
        $this->update(['is_full' => true]);
    }

    public function makeNotFull(): void
    {
        $this->update(['is_full' => false]);
    }
}
