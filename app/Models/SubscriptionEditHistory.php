<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionEditHistory extends Model
{
    use HasFactory;

    protected $table = 'subscriptions_edits_history';

    protected $fillable = [
        'subscription_id',
        'old_amount',
        'new_amount'
    ];
}
