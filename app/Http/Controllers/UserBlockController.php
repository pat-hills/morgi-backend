<?php

namespace App\Http\Controllers;

use App\Logger\Logger;
use App\Models\PubnubChannel;
use App\Models\RookieSaved;
use App\Models\RookieSeen;
use App\Models\RookieSeenHistory;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserBlock;
use App\Utils\NotificationUtils;
use App\Utils\UserBlockUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserBlockController extends Controller
{
    public function store(Request $request, User $user): JsonResponse
    {
        /*
         * Request's validation
         */
        $validation = $this->storeValidation($request, $user);
        if(isset($validation)){
            return $validation;
        }

        $requesting_user = $request->user();

        try {
            $leader_user = UserBlockUtils::getLeaderUser($requesting_user, $user);
            $rookie_user = UserBlockUtils::getRookieUser($requesting_user, $user);
        }catch (\Exception $exception){
            return response()->json(['message' => "You cannot block a " . ucfirst($user->type)], 400);
        }

        DB::beginTransaction();
        try {

            $user_block = UserBlockUtils::createUserBlock($requesting_user, $user);
            $subscription = UserBlockUtils::endSubscription($rookie_user, $leader_user, $user_block);

            /*
             * If requesting user is rookie and isset subscription, refund latest transaction
             */
            if(isset($subscription) && $requesting_user->id === $rookie_user->id){
                $transaction = UserBlockUtils::refundLatestSubscriptionTransaction($subscription, $user_block);
            }

            /*
             * Remove some other rows
             */
            RookieSeen::query()
                ->where('rookie_id', $rookie_user->id)
                ->where('leader_id', $leader_user->id)
                ->delete();
            RookieSeenHistory::query()
                ->where('rookie_id', $rookie_user->id)
                ->where('leader_id', $leader_user->id)
                ->delete();
            RookieSaved::query()
                ->where('rookie_id', $rookie_user->id)
                ->where('leader_id', $leader_user->id)
                ->delete();

            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            Logger::logException($exception);
            return response()->json(['message' => "Unable to block this user. Please try later!"], 400);
        }

        /*
         * Send notifications
         */
        try {

            if($requesting_user->id===$rookie_user->id){
                NotificationUtils::sendNotification($leader_user->id, "rookie_blocked_leader", now(), [
                    'ref_user_id' => $rookie_user->id
                ]);

                NotificationUtils::sendNotification($rookie_user->id, "blocked_leader", now(), [
                    'ref_user_id' => $leader_user->id,
                    'amount' => (isset($transaction))
                        ? $transaction->dollars ?? 0
                        : 0
                ]);
            }

            if($requesting_user->id===$leader_user->id){
                NotificationUtils::sendNotification($rookie_user->id, "leader_blocked_rookie", now(), [
                    'ref_user_id' => $leader_user->id
                ]);
            }

        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        return response()->json(['message' => "User successfully blocked"]);
    }

    public function delete(Request $request, User $user): JsonResponse
    {
        /*
         * Request's validation
         */
        $validation = $this->deleteValidation($request, $user);
        if(isset($validation)){
            return $validation;
        }

        $requesting_user = $request->user();

        try {
            $leader_user = UserBlockUtils::getLeaderUser($requesting_user, $user);
            $rookie_user = UserBlockUtils::getRookieUser($requesting_user, $user);
        }catch (\Exception $exception){
            Logger::logException($exception);
            return response()->json(['message' => "You cannot block a " . ucfirst($user->type)], 400);
        }

        $user_block = UserBlock::whereNull('deleted_at')
            ->where('from_user_id', $requesting_user->id)
            ->where('to_user_id', $user->id)
            ->first();
        if(!isset($user_block)){
            return response()->json(['message' => "You did not blocked this user."], 400);
        }

        DB::beginTransaction();
        try {
            Subscription::query()
                ->where('rookie_id', $rookie_user->id)
                ->where('leader_id', $leader_user->id)
                ->where('user_block_id', $user_block->id)
                ->update(['user_block_id' => null]);

            $pubnub_channel = PubnubChannel::query()
                ->where('rookie_id', $rookie_user->id)
                ->where('leader_id', $leader_user->id)
                ->where('user_block_id', $user_block->id)
                ->first();

            if(isset($pubnub_channel)){

                $pubnub_channel->user_block_id = null;
                if($pubnub_channel->is_referral){
                    $pubnub_channel->active = true;
                }

                $pubnub_channel->save();
            }

            $user_block->delete();
            DB::commit();
        }catch (\Exception $exception){
            DB::beginTransaction();
            Logger::logException($exception);
            return response()->json(['message' => "Unable to unblock this user. Please try later!"], 400);
        }

        return response()->json(null, 204);
    }

    private function deleteValidation(Request $request, User $user)
    {
        $requesting_user = $request->user();
        if(!$user->active){
            return response()->json(['message' => "This user is inactive."], 403);
        }

        $requesting_user_did_not_blocked_user = UserBlock::whereNull('deleted_at')
            ->where('from_user_id', $requesting_user->id)
            ->where('to_user_id', $user->id)
            ->exists();
        if(!$requesting_user_did_not_blocked_user){
            return response()->json(['message' => "You did not blocked this user."], 400);
        }

        $user_blocked_requesting_user = UserBlock::whereNull('deleted_at')
            ->where('from_user_id', $user->id)
            ->where('to_user_id', $requesting_user->id)
            ->exists();
        if($user_blocked_requesting_user){
            return response()->json(['message' => "This user blocked you."], 400);
        }

        return null;
    }

    private function storeValidation(Request $request, User $user)
    {
        $requesting_user = $request->user();
        if(!$user->active){
            return response()->json(['message' => "This user is inactive."], 403);
        }

        $requesting_user_already_blocked_user = UserBlock::whereNull('deleted_at')
            ->where('from_user_id', $requesting_user->id)
            ->where('to_user_id', $user->id)
            ->exists();
        if($requesting_user_already_blocked_user){
            return response()->json(['message' => "You already blocked this user."], 400);
        }

        $user_already_blocked_requesting_user = UserBlock::whereNull('deleted_at')
            ->where('from_user_id', $user->id)
            ->where('to_user_id', $requesting_user->id)
            ->exists();
        if($user_already_blocked_requesting_user){
            return response()->json(['message' => "This user already blocked you."], 400);
        }

        return null;
    }
}
