<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintHistory extends Model
{
    use HasFactory;

    protected $table = 'complaints_histories';

    protected $fillable = [
        'action',
        'admin_id',
        'note',
        'complaint_id'
    ];
}
