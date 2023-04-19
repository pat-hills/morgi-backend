<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEmailSent extends Model
{
    use HasFactory;

    protected $table = 'users_emails_sent';

    protected $fillable = [
        'user_id',
        'type',
        'errors',
        'sendgrid_message_id',
        'sent',
        'clicked_at',
        'opened_at'
    ];
}
