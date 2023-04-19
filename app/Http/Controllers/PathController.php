<?php

namespace App\Http\Controllers;

use App\Http\Resources\PathResource;
use App\Models\Path;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class PathController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'count' => ['sometimes', 'boolean']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $cache_reference = "paths:{$request->response_type}:count:" . $request->get('count', 0);
        $paths = Cache::tags('paths')->get($cache_reference);
        if (isset($paths)) {
            return response()->json($paths);
        }

        $paths = Path::query()->where('is_subpath', false)->orderBy('name')->get();

        $paths = PathResource::compute(
            $request,
            $paths
        )->get();

        Cache::tags('paths')->put($cache_reference, $paths, 3600);

        return response()->json($paths);
    }

    public function indexSubpath(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'path_id' => ['sometimes', 'exists:paths,id'],
            'count' => ['sometimes', 'boolean']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $cache_reference = "subpaths:{$request->response_type}:count:" . $request->get('count', 0);
        if (isset($request->path_id)) {
            $cache_reference .= ":path_id:{$request->path_id}";
        }

        $subpaths = Cache::tags('paths')->get($cache_reference);
        if (isset($subpaths)) {
            return response()->json($subpaths);
        }

        $subpaths = Path::query()->selectRaw('paths.*')
            ->leftJoin('users_paths', 'users_paths.path_id', '=', 'paths.id')
            ->where('paths.is_subpath', true)
            ->when($request->path_id, function ($query, $path_id){
                $query->where('paths.parent_id', $path_id);
            });

        if($request->boolean('count')){
            $subpaths = $subpaths->havingRaw("COUNT(users_paths.id) > 0");
        }

        $subpaths = $subpaths->groupBy('paths.id')
            ->orderBy('paths.name')
            ->get();

        $response = PathResource::compute($request, $subpaths)->get();

        Cache::tags('paths')->put($cache_reference, $response, 3600);

        return response()->json($response);
    }

    public function getUnlockedPaths(Request $request)
    {
        $user = $request->user();
        $paths = Path::query()->selectRaw('paths.*, COUNT(users_paths.id) as users_count')
            ->join('users_paths', 'paths.id', '=', 'users_paths.path_id')
            ->where('users_paths.user_id', $user->id)
            ->groupBy('paths.id')
            ->orderBy('users_count')
            ->get();

        $paths = PathResource::compute($request, $paths)->get();

        return response()->json($paths);
    }

    public function getLockedPaths(Request $request)
    {
        $user = $request->user();

        $unlocked_paths_ids = Path::query()->selectRaw("paths.id")
            ->join('users_paths', 'paths.id', '=', 'users_paths.path_id')
            ->where('users_paths.user_id', $user->id)
            ->groupBy('paths.id')
            ->pluck('id')
            ->toArray();

        $locked_paths = Path::query()->selectRaw('paths.*, COUNT(users_paths.id) as users_count')
            ->join('users_paths', 'paths.id', '=', 'users_paths.path_id')
            ->where('paths.is_subpath', false)
            ->whereNotIn('paths.id', $unlocked_paths_ids)
            ->groupBy('paths.id')
            ->orderBy('users_count')
            ->get();

        $locked_paths = PathResource::compute($request, $locked_paths)->get();

        return response()->json($locked_paths);
    }
}

