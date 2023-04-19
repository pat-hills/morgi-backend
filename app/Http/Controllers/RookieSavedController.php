<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Leader;
use App\Models\Rookie;
use App\Models\RookieSaved;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RookieSavedController extends Controller
{
    public function saveRookie(Request $request, Rookie $rookie)
    {
        $validator = Validator::make($request->all(), [
            'photo_id' => ['sometimes', 'exists:photos,id'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $leader_user = $request->user();

        if (!$rookie->active){
            return response()->json(['message' => trans('auth.account_not_active')], 400);
        }

        if ($rookie->hasBlockedLeader($leader_user->id)){
            return response()->json(['message' => 'This rookie has blocked you'], 403);
        }

        if(isset($request->photo_id) && !$rookie->hasPhoto($request->photo_id)){
            $request->photo_id = null;
        }

        $leader = Leader::query()->find($leader_user->id);

        $profile_saved = RookieSaved::where('leader_id', $leader_user->id)->where('rookie_id', $rookie->id)->first();
        if($profile_saved){
            return response()->json(['message' => trans('leader.rookie_save_error')], 400);
        }

        RookieSaved::create([
            'leader_id' => $leader_user->id,
            'rookie_id' => $rookie->id,
            'photo_id' => $request->photo_id
        ]);

        $action_tracking = $leader->retrieveOrCreateActionTracking($rookie->id);
        $action_tracking->update([
            'saved_profile' => true
        ]);

        $response = UserResource::compute(
            $request,
            User::query()->find($rookie->id)
        )->first();

        return response()->json($response, 201);
    }

    public function unsaveRookie(Request $request, Rookie $rookie)
    {
        $leader_user = $request->user();

        $profile_saved = RookieSaved::where('leader_id', $leader_user->id)->where('rookie_id', $rookie->id)->first();
        if(!$profile_saved){
            return response()->json(['message' => trans('leader.rookie_unsave_error')], 404);
        }

        $profile_saved->delete();

        $response = UserResource::compute(
            $request,
            User::query()->find($rookie->id)
        )->first();

        return response()->json($response);
    }

    public function getRookiesSaved(Request $request)
    {
        $user = $request->user();
        $rookies = User::query()->select('users.*')
            ->join('rookies_saved', 'users.id', '=', 'rookies_saved.rookie_id')
            ->where('rookies_saved.leader_id', $user->id)
            ->orderByDesc('rookies_saved.created_at')
            ->get();

        $response = UserResource::compute(
            $request,
            $rookies
        )->get();

        return response()->json($response);
    }
}
