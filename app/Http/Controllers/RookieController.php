<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Logger\Logger;
use App\Models\Rookie;
use App\Models\RookieWinnerHistory;
use App\Models\User;
use App\Transactions\Morgi\TransactionRookieMorgiBonus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RookieController extends Controller
{
    public function showRookie(Request $request, $identifier)
    {
        if(!is_numeric($identifier) && !$request->is_username){
            return response()->json(['message' => 'You must send an ID'], 400);
        }

        $requesting_user = $request->user();

        if($request->is_username){

            $user = User::query()->select('id')->where('username', $identifier)->first();
            if(!$user){
                return response()->json(['message' => 'User not found'], 404);
            }

            $identifier = $user->id;
        }

        $rookie = Rookie::query()->find($identifier);
        $rookie_user = User::query()->find($identifier);

        if(!isset($rookie_user)){
            return response()->json(['message' => 'Rookie not found'], 404);
        }

        if (!$rookie_user->active){
            return response()->json(['message' => 'This rookie is no longer active!'], 400);
        }

        if ($rookie->hasBlockedLeader($requesting_user->id)){
            return response()->json(['message' => 'This rookie has blocked you'], 403);
        }

        $response = UserResource::compute(
            $request,
            $rookie_user
        )->first();

        return response()->json($response);
    }

    public function showPublicRookie(Request $request, string $username)
    {
        $rookie = User::query()->where('username', $username)->first();
        if(!isset($rookie)){
            return response()->json([], 404);
        }

        if (!$rookie->active){
            return response()->json(['message' => 'This rookie is no longer active!'], 400);
        }

        $response = UserResource::compute(
            $request,
            $rookie
        )->first();

        return response()->json($response);
    }

    public function seenRookieWin(Request $request)
    {
        $rookie_user = $request->user();
        $rookie_winner = RookieWinnerHistory::query()
            ->where('rookie_id', $rookie_user->id)
            ->whereNull('seen_at')
            ->whereNull('transaction_id')
            ->whereDate('win_at', '>=', Carbon::now()->subDay())
            ->first();

        if(!isset($rookie_winner)){
            return response()->json([], 404);
        }

        $rookie_winner->update(['seen_at' => now()]);

        DB::beginTransaction();
        try {
            $transaction = TransactionRookieMorgiBonus::create(
                $rookie_user->id,
                $rookie_winner->amount,
                "Morgi Ring Lottery win reward!"
            );

            $rookie_winner->update([
                'transaction_id' => $transaction->id
            ]);
            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            $rookie_winner->update(['seen_at' => null]);
            Logger::logException($exception);
            return response()->json(['message' => "Error during the creation of the transaction"], 500);
        }

        return response()->json([]);
    }
}
