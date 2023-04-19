<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReferralEmailsSent extends Model
{
    use HasFactory;

    protected $table = 'users_referral_emails_sent';

    protected $fillable = [
        'user_id',
        'email',
        'user_joined',
        'referred_user_id'
    ];

    protected $casts = [
        'user_joined' => 'boolean'
    ];
}
