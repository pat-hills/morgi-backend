<?php

namespace App\Models;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoalDonation extends Model
{
    use HasFactory;

    public const STATUS_CHARGEBACK = 'chargeback';
    public const STATUS_REFUND = 'refund';
    public const STATUS_SUCCESSFUL = 'successful';

    protected $fillable = [
        'amount', 'goal_id', 'leader_id', 'transaction_id', 'is_withdrawn'
    ];

    public function leader()
    {
        return $this->belongsTo(User::class,'leader_id');
    }

    public function goal()
    {
        return $this->belongsTo(Goal::class,'goal_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class,'transaction_id');
    }
}
