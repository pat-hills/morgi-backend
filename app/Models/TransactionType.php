<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionType extends Model
{
    use HasFactory;

    protected $table = 'transactions_types';
    public $timestamps = false;

    protected $fillable = [
        'lang',
        'description_leader',
        'description_rookie',
        'type'
    ];
}
