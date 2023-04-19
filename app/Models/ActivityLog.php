<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activities_logs';

    protected $fillable = [
        'internal_id',
        'transaction_internal_id',
        'initiated_by',
        'rookie_id',
        'leader_id',
        'micromorgi',
        'morgi',
        'refund_type',
        'admin_id',
        'refunded_at',
        'dollars'
    ];

    protected $appends = [
        'leader',
        'rookie',
        'real_initiated_by'
    ];

    public function getLeaderAttribute()
    {
        $user = User::query()->find($this->leader_id);
        return (isset($user)) ? $user->setAppends([])->makeVisible('email') : null;
    }

    public function getRookieAttribute()
    {
        $user = User::query()->find($this->rookie_id);
        return (isset($user)) ? $user->setAppends([])->makeVisible('email') : null;
    }

    public function getRealInitiatedByAttribute()
    {
        if(!isset($this->initiated_by)){

            $email = User::query()->find($this->leader_id)->email ?? null;
            if(!isset($email)){
                $email = User::query()->find($this->rookie_id)->email ?? null;
            }

            if(isset($email)){
                return $email;
            }
        }

        return $this->initiated_by;
    }

    public function getTypeAttribute()
    {
        if($this->transaction_internal_id === null){
            if ($this->morgi){
                return 'EDIT GIFT';
            }
            return null;
        }

        try {
            $transaction = Transaction::query()->where('internal_id', $this->transaction_internal_id)->first();
            if($transaction->type == 'refund'){
                $transaction = Transaction::query()->where('internal_id', $transaction->referal_internal_id)->first();
                return "REFUNDED - " . strtoupper(str_replace('_', ' ', $transaction->type));
            }
        }catch (\Exception $exception){
            return null;
        }

        return strtoupper(str_replace('_', ' ', $transaction->type));
    }
}
