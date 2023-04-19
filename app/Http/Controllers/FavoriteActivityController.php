<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FavoriteActivity; 
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\FavoriteActivityResource;

class FavoriteActivityController extends Controller
{

    public function store(Request $request)
    { 
        $validator = Validator::make($request->all(), [
            'name' => ['required','string']
        ]); 
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $activity_name = ucfirst(strtolower(trim($request->name)));
        $activity_key_name = str_replace(' ', '_', strtolower(trim($request->name)));

        $check_activity_name = FavoriteActivity::query()->where('key_name', $activity_key_name)->exists();

        if($check_activity_name)
        {
            return response()->json(['message' => "Favorite Activity already exits"],400);
        }

        $favourites_activity = FavoriteActivity::create([
            'name' => $activity_name,
            'key_name' => $activity_key_name
        ]);

        $response = FavoriteActivityResource::compute(
            $request,
            $favourites_activity
        )->first();

        return response()->json($response, 201);

    }
    
    public function index(Request $request)
    {
        $favourites_activities = FavoriteActivity::query();

        if(isset($request->sort_by, $request->sort_direction) && $request->sort_by === 'users_favorite_activities_count'){
            $favourites_activities = $favourites_activities
            ->withCount('usersFavoriteActivities')
            ->orderBy('users_favorite_activities_count', 'desc');
        }

        $favourites_activities = $favourites_activities->get();

        $response = FavoriteActivityResource::compute(
            $request,
            $favourites_activities
        )->get();

        return response()->json($response, 201);
    }
}
