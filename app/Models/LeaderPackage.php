<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaderPackage extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = "leaders_packages";

    protected $fillable = [
        'amount',
        'amount_spent',
        'leader_payment_id',
        'leader_id',
        'amount_available',
        'is_refunded',
        'transaction_id'
    ];

    protected $casts = [
        'is_refunded' => 'boolean'
    ];

    public function spendMicromorgi(int $micromorgi): void
    {
        $this->update([
            'amount_spent' => $this->amount_spent + $micromorgi,
            'amount_available' => $this->amount_available - $micromorgi,
        ]);
    }
}
