<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SendGridEmailCheck extends Model
{
    use HasFactory;

    protected $table = 'sendgrid_emails_checks';

    protected $fillable = [
        'type',
        'emails_count'
    ];
}
