<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileAlertCode extends Model
{
    use HasFactory;

    protected $table = 'profiles_alerts_codes';

    public $timestamps = false;

    protected $fillable = [
        'code',
        'message'
    ];
}
