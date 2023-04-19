<?php

namespace App\Http\Controllers;

use App\Http\Resources\GoalResource;
use App\Models\Goal;
use App\Models\Leader;
use App\Utils\UserUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SavedGoalController extends Controller
{
    public function index(Request $request)
    {
        $leader = Leader::find(Auth::id());

        $blocked_ids = UserUtils::getUsersBlockIds($request->user()->id);

        $goals = $leader->savedGoals()->pluck('goals.id');
        $goals = Goal::query()
            ->whereNotIn('goals.rookie_id', $blocked_ids)
            ->whereIn('goals.id', $goals);

        if ($request->has('order_by')){
            $goals = $goals->orderBy($request->order_by, strtoupper($request->order_direction));
        }
        if ($request->has('time_range')){
            $goals = $goals->timeRangeInHours($request->time_range);
        }

        if (!empty($request->path_ids) || !empty($request->subpath_ids)){

            $goals = $goals->join('users_paths', 'goals.rookie_id', '=', 'users_paths.user_id');
            $paths_ids = [];
            if (!empty($request->path_ids)){
                $paths_ids = array_merge($paths_ids, $request->path_ids);
            }

            if (!empty($request->subpath_ids)){
                $paths_ids = array_merge($paths_ids, $request->subpath_ids);
            }

            $goals = $goals->whereIn('users_paths.path_id', $paths_ids);
        }

        if ($request->has('type_id')){
            $goals = $goals->where('type_id', $request->type_id);
        }

        $goals->groupBy('goals.id');

        $goals = $goals->paginate($request->get('limit', 15));

        $response = GoalResource::compute($request, $goals)->get();
        return response()->json($response);
    }

    public function store(Request $request, Goal $goal)
    {
        $leader = Leader::find(Auth::id());
        $leader->savedGoals()->attach($goal->id);

        $response = GoalResource::compute($request, $goal)->first();
        return response()->json($response);
    }

    public function unSave(Request $request, Goal $goal)
    {
        $leader = Leader::find(Auth::id());
        $leader->savedGoals()->detach($goal->id);

        $response = GoalResource::compute($request, $goal)->first();
        return response()->json($response);
    }
}
