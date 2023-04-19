<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaderQuotation extends Model
{
    use HasFactory;

    protected $table = 'leaders_quotations';

    protected $fillable = [
        'user_id',
        'text'
    ];
}
