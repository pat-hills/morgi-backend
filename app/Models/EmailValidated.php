<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailValidated extends Model
{
    use HasFactory;

    protected $table = "emails_validated";

    protected $fillable = [
        'email',
        'verdict',
        'score',
        'local',
        'host',
        'suggestion',
        'has_valid_address_syntax',
        'has_mx_or_a_record',
        'is_suspected_disposable_address',
        'is_suspected_role_address',
        'has_known_bounces',
        'has_suspected_bounces',
        'source',
        'ip_address'
    ];

    protected $casts = [
        'has_valid_address_syntax' => 'boolean',
        'has_mx_or_a_record' => 'boolean',
        'is_suspected_disposable_address' => 'boolean',
        'is_suspected_role_address' => 'boolean',
        'has_known_bounces' => 'boolean',
        'has_suspected_bounces' => 'boolean',
        'score' => 'double'
    ];
}
