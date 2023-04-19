<?php


namespace App\Utils;


use App\Models\PubnubChannel;
use App\Models\RookieSeen;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserBlock;
use Carbon\Carbon;

class UserBlockUtils
{
    public static function createUserBlock(User $requesting_user, User $user): UserBlock
    {
        $user_block = UserBlock::create([
            'from_user_id' => $requesting_user->id,
            'to_user_id' => $user->id
        ]);

        $rookie_id = ($requesting_user->type==='rookie')
            ? $requesting_user->id
            : $user->id;

        $leader_id = ($requesting_user->type==='leader')
            ? $requesting_user->id
            : $user->id;

        RookieSeen::query()->where('rookie_id', $rookie_id)->delete();

        PubnubChannel::query()
            ->where('leader_id', $leader_id)
            ->where('rookie_id', $rookie_id)
            ->update([
                'active' => false,
                'user_block_id' => $user_block->id
            ]);

        return $user_block;
    }

    public static function endSubscription(User $rookie_user, User $leader_user, UserBlock $user_block): ?Subscription
    {
        $subscription = Subscription::where('rookie_id', $rookie_user->id)
            ->where('leader_id', $leader_user->id)
            ->first();

        if(!isset($subscription)){
            return null;
        }

        $subscription->update([
            'status' => 'canceled',
            'canceled_at' => now(),
            'user_block_id' => $user_block->id,
            'deleted_at' => now(),
            'valid_until_at' => now(),
            'sent_reply_reminder_email_at' => null
        ]);

        return $subscription;
    }

    public static function refundLatestSubscriptionTransaction(Subscription $subscription, UserBlock $user_block): ?Transaction
    {
        $transaction = Transaction::where('subscription_id', $subscription->id)
            ->where('type', 'gift')
            ->whereNull('refund_type')
            ->where('created_at', '>=', Carbon::now()->subMonth())
            ->latest()
            ->first();

        if(!isset($transaction)){
            return null;
        }

        try {
            TransactionRefundUtils::config($transaction)->refund(null, null, false, $user_block->id);
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        return $transaction;
    }

    public static function getRookieUser(User $requesting_user, User $user): User
    {
        if($requesting_user->type==='rookie' && $user->type==='rookie'){
            throw new \Exception("Invalid users provided");
        }

        if($requesting_user->type==='rookie'){
            return $requesting_user;
        }

        if($user->type==='rookie'){
            return $user;
        }

        throw new \Exception("Invalid users provided");
    }

    public static function getLeaderUser(User $requesting_user, User $user): User
    {
        if($requesting_user->type==='leader' && $user->type==='leader'){
            throw new \Exception("Invalid users provided");
        }

        if($requesting_user->type==='leader'){
            return $requesting_user;
        }

        if($user->type==='leader'){
            return $user;
        }

        throw new \Exception("Invalid users provided");
    }
}
