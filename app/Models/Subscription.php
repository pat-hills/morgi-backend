<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'leader_id',
        'rookie_id',
        'photo_id',
        'amount',
        'status',
        'subscription_at',
        'last_subscription_at',
        'leader_payment_method_id',
        'canceled_at',
        'user_block_id',
        'failed_at',
        'next_donation_at',
        'valid_until_at',
        'ended_by',
        'sent_reply_reminder_email_at',
        'deleted_at',
        'type'
    ];

    protected $hidden = [
        'photo_id',
        'leader_payment_method_id',
        'created_at',
        'updated_at'
    ];

    protected $appends = [
        'is_recurring',
    ];

    public function scopeSearch(Builder $query, int $leader_id, int $rookie_id): Builder
    {
        return $query->where('leader_id', $leader_id)->where('rookie_id', $rookie_id);
    }

    public function getIsRecurringAttribute(): bool
    {
        return $this->status === 'active';
    }

    public function getNextDonationAtAttribute($value)
    {
        return ($this->status==='canceled') ? null : $value;
    }

    public function getValidUntilAtAttribute($value)
    {
        return ($this->status==='active') ? null : $value;
    }

    public function getTransactionsAttribute(): array
    {
        $transactions = Transaction::query()
            ->select(
                "transactions.created_at",
                "transactions.morgi",
                "transactions.taxed_morgi",
                "leaders_payments.status",
                "transactions.refund_type"
            )
            ->join('leaders_payments', "transactions.leader_payment_id", '=', "leaders_payments.id")
            ->where("transactions.type", 'gift')
            ->where("transactions.subscription_id", $this->id)
            ->get();

        $tot_trans = $transactions->where('status', 'paid')->sum('morgi');
        $taxed_tot_morgi = $transactions->where('status', 'paid')->sum('taxed_morgi');

        return [
            'id' => $this->id,
            'tot_trans' => $tot_trans,
            'taxed_tot_morgi' => $taxed_tot_morgi,
            'count' => count($transactions),
            'transactions' => $transactions
        ];
    }
}
