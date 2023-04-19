<?php

namespace App\Http\Controllers;

use App\Http\Resources\SubscriptionResource;
use App\Http\Resources\TransactionHandshakeResource;
use App\Http\Resources\UserResource;
use App\Models\LeaderSawRookie;
use App\Models\Nickname;
use App\Models\Rookie;
use App\Models\RookieSeen;
use App\Models\RookieSeenHistory;
use App\Models\SmsSent;
use App\Models\TransactionHandshake;
use App\Models\User;
use App\Models\Leader;
use App\Models\Subscription;
use App\Models\UserBlock;
use App\Models\UserReferralEmailsSent;
use App\Rules\EmailValidation;
use App\Services\Mailer\Mailer;
use App\Services\Sms\Sms;
use App\Utils\UserUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LeaderController extends Controller
{
    /*
     * This is the list of leaders with opened chat with his nickname
     * TODO: resource
     */
    public function chatIndex(Request $request)
    {
        $rookie = Rookie::find(Auth::id());
        $blocked_ids = UserUtils::getUsersBlockIds($rookie->id);

        $leaders = $rookie
            ->chattingLeaders()
            ->select('leaders.*', 'users.username')
            ->join('users', 'leaders.id', '=', 'users.id')
            ->where('users.active', true)
            ->whereNotIn('users.id', $blocked_ids)
            ->with(['nickname' => function ($query) use ($rookie) {
                $query->where('nicknamer_id', '=', $rookie->id);
            }])
            ->get();

        return response()->json($leaders);
    }

    public function leadersList(Request $request)
    {
        $user = $request->user();
        $subscriptions = Subscription::query()
            ->where('rookie_id', $user->id)
            ->whereIn('status', ['active', 'canceled'])
            ->whereDate('last_subscription_at', '>=', now()->subDays(30))
            ->whereNull('deleted_at')
            ->whereNull('user_block_id')
            ->get();

        $response = SubscriptionResource::compute(
            $request,
            $subscriptions
        )->get();

        return response()->json($response);
    }

    public function activeMorgiGifting(Request $request)
    {
        $user = $request->user();
        $rookies_blocked_leader = UserBlock::select('from_user_id')
            ->whereNull('deleted_at')
            ->where('to_user_id', $user->id)
            ->pluck('from_user_id')
            ->toArray();

        $subscriptions = Subscription::query()->select('subscriptions.*')
            ->join('users', 'users.id', '=', 'subscriptions.rookie_id')
            ->where('subscriptions.leader_id', $user->id)
            ->whereNull('subscriptions.deleted_at')
            ->whereNull('subscriptions.user_block_id')
            ->whereIn('subscriptions.status', ['active', 'canceled'])
            ->whereDate('subscriptions.valid_until_at', '>=', now()->toDateString())
            ->where('users.active', true)
            ->whereNotIn('users.id', $rookies_blocked_leader)
            ->orderBy('subscriptions.subscription_at', 'desc')
            ->get();

        $response = SubscriptionResource::compute(
            $request,
            $subscriptions
        )->get();

        return response()->json($response);
    }

    public function getPublicLeaders(Request $request)
    {
        $leaders_to_take = env('PUBLIC_LEADERS_TO_TAKE', 10);

        $leaders = User::query()->where('active', true);
        if($leaders_to_take > $leaders->count()){
            return response()->json([]);
        }

        $leaders = $leaders->inRandomOrder()
            ->take($leaders_to_take)
            ->get();

        $response = UserResource::compute(
            $request,
            $leaders
        )->get();

        return response()->json($response);
    }

    public function show(Request $request, Leader $leader)
    {
        $requesting_user = $request->user();
        $user = User::find($leader->id);

        $response = UserResource::compute(
            $request,
            $user
        )->first();

        $response->micro_morgi_given = $leader->getMicroMorgiGivenToRookie($requesting_user->id);
        $response->morgi_given = $leader->getMorgiGivenToRookie($requesting_user->id);

        return response()->json($response);
    }

    public function latestPayment(Request $request)
    {
        $user = $request->user();
        $transaction_handshake = TransactionHandshake::query()
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        if(!isset($transaction_handshake)){
            return response()->json([], 404);
        }

        $response = TransactionHandshakeResource::compute(
            $request,
            $transaction_handshake
        )->first();

        return response()->json($response);
    }

    public function setNickname(Request $request, Leader $leader)
    {
        $user = $request->user();
        $rookie = Rookie::find($user->id);

        $validator = Validator::make($request->all(), [
            'nickname' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([$validator->errors()], 400);
        }

        $nickname = Nickname::create([
            'nickname' => $request->nickname,
            'nicknamer_id' => $rookie->id,
            'nicknamed_id' => $leader->id
        ]);

        return response()->json($nickname);
    }

    public function removeNickname(Request $request, Leader $leader, Nickname $nickname)
    {
        $user = $request->user();
        $nickname = Nickname::where('id', $nickname->id)
            ->where('nicknamer_id', $user->id)
            ->where('nicknamed_id', $leader->id)
            ->first();

        if(!isset($nickname)){
            return response()->json([], 404);
        }

        Nickname::destroy($nickname->id);

        return response()->json([], 204);
    }

    public function updateNickname(Request $request, Leader $leader, Nickname $nickname)
    {
        $user = $request->user();
        $nickname = Nickname::where('id', $nickname->id)
            ->where('nicknamer_id', $user->id)
            ->where('nicknamed_id', $leader->id)
            ->first();

        if(!isset($nickname)){
            return response()->json([], 404);
        }

        $nickname->nickname = $request->nickname;
        $nickname->save();

        return response()->json($nickname);
    }
}
