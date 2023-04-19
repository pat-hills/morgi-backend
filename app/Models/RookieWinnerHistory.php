<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RookieWinnerHistory extends Model
{
    use HasFactory;

    public $table = 'rookies_winners_histories';
    public $timestamps = false;

    public $fillable = [
        'rookie_id',
        'amount',
        'transaction_id',
        'win_at',
        'seen_at'
    ];
}
