<?php

namespace App\Http\Controllers;

use App\Enums\ActionTrackingEnum;
use App\Models\Impression;
use App\Models\Leader;
use App\Models\Rookie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ActionTrackingController extends Controller
{
    public static function store(Request $request, Rookie $rookie): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'action' => ['required', 'string', Rule::in(ActionTrackingEnum::ACTIONS)],
            'time_in_rookie_profile_in_seconds' => ['integer', Rule::requiredIf($request->action==='time_in_rookie_profile_in_seconds')]
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $leader_user = $request->user();
        if ($rookie->hasBlockedLeader($leader_user->id)){
            return response()->json(['message' => 'This rookie has blocked you'], 403);
        }

        $leader = Leader::query()->find($leader_user->id);
        $action_tracking = $leader->retrieveOrCreateActionTracking($rookie->id);

        switch ($request->action){

            case "clicked_profile":
                $action_tracking->update(['clicked_profile' => true]);
                break;

            case "saw_video":
                $action_tracking->update(['saw_video' => true]);
                break;

            case "shared_profile":
                $action_tracking->update(['shared_profile' => true]);
                break;

            case "time_in_rookie_profile_in_seconds":
                $action_tracking->update([
                    'time_in_rookie_profile_in_seconds' => $request->time_in_rookie_profile_in_seconds + $action_tracking->time_in_rookie_profile_in_seconds
                ]);
                break;

            default:
                return response()->json(['message' => "Invalid action provided"], 400);
        }

        Impression::create([
            'from_user_id' => $leader->id,
            'to_user_id' => $rookie->id,
            'type' => $request->action
        ]);

        return response()->json([]);
    }
}
