<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PubnubGroup extends Model
{
    use HasFactory;

    protected $table = 'pubnub_groups';

    protected $fillable = [
        'name',
        'category',
        'user_id'
    ];
}
