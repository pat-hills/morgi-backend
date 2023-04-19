<?php

namespace App\Models;

use App\Enums\TransactionRefundEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionRefund extends Model
{
    use HasFactory;

    protected $table = 'transactions_refunds';

    protected $fillable = [
        'status',
        'transaction_id',
        'admin_id',
        'refund_reason',
        'error',
        'approved_at',
        'failed_at'
    ];

    public function approve(): void
    {
        $this->update([
            'status' => TransactionRefundEnum::STATUS_APPROVED,
            'approved_at' => now()
        ]);
    }

    public function fail(string $error = null): void
    {
        $this->update([
            'status' => TransactionRefundEnum::STATUS_FAILED,
            'error' => $error,
            'failed_at' => now()
        ]);
    }

}
