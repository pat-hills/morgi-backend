<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintNote extends Model
{
    use HasFactory;

    protected $table = 'complaints_notes';

    protected $fillable = [
        'complaint_id',
        'admin_id',
        'note'
    ];
}
