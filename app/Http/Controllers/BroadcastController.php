<?php

namespace App\Http\Controllers;

use App\Http\Resources\BroadcastResource;
use App\Models\Broadcast;
use App\Models\BroadcastMessage;
use App\Models\Goal;
use App\Models\GoalDonation;
use App\Models\Leader;
use App\Models\PubnubChannel;
use App\Models\User;
use App\Services\Chat\Chat;
use App\Utils\BroadcastUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BroadcastController extends Controller
{

    public function index(Request $request)
    {
        $user = $request->user();
        $broadcasts = $user->goalBroadcasts();

        if ($request->has('is_goal')) {
            $param = $request->boolean('is_goal');
            $broadcasts = $broadcasts->where('is_goal', $param);
        }

        $broadcasts = $broadcasts->get();
        $response = BroadcastResource::compute($request, $broadcasts)->get();

        return response()->json($response);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $data = $request->only('users','goals','message');

        $validator = Validator::make($request->all(), [
            'users' => 'array',
            'goals' => 'array',
            'user.*' => 'exists:users,id',
            'goals.*' => 'exists:goals,id',
            'message' => 'required_without:media',
            'media' => 'sometimes',
            'type' => [
                'required_with:media',
                'in:image,video'
            ],
            'teaser' => 'sometimes',
            'goal_id' => [
                'required_with:teaser',
                'exists:goals,id'
            ]
        ]);

        if($validator->fails()){
            return response()->json([$validator->errors()], 400);
        }

        DB::beginTransaction();
        try {
            $goals = Goal::query()
                ->whereIn('id', $data['goals'])
                ->where('rookie_id','=', $user->id)
                ->get()
                ->pluck('id');

            $broadcast = Broadcast::create([
               'sender_id' => $user->id
            ]);

            $broadcast->goals()->attach($goals);
            $broadcast->users()->attach($data['users']);
            if($request->has('media')){
                BroadcastUtils::broadcastMediaMessage(
                    $goals->toArray(),
                    $data['users'],
                    $data['message'],
                    $user,
                    $broadcast,
                    $request->media,
                    $request->type
                );
            } elseif ($request->has('teaser')) {
                BroadcastUtils::broadcastTeaserMessage(
                    $goals->toArray(),
                    $data['users'],
                    $data['message'],
                    $user,
                    $broadcast,
                    $request->goal_id,
                );
            } else {
                BroadcastUtils::broadcastMessage(
                    $goals->toArray(),
                    $data['users'],
                    $data['message'],
                    $user,
                    $broadcast
                );
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
        $broadcast = BroadcastResource::compute($request, $broadcast->refresh())->first();
        return response()->json($broadcast, 201);
    }

    public function show(Request $request, Broadcast $broadcast){
        $user_id = Auth::id();
        if ($broadcast->sender_id !== $user_id) {
            return response()->json(["message" => "Unauthorized"], 403);
        }
        $broadcast = BroadcastResource::compute($request,$broadcast)->first();
        return response()->json($broadcast);
    }

    public function sendMessage(Request $request, Broadcast $broadcast){

        $user = $request->user();
        if ($broadcast->sender_id !== $user->id) {
            return response()->json(["message" => "Unauthorized"], 403);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required_without:media',
            'media' => 'sometimes',
            'type' => [
                'required_with:media',
                'in:image,video'
            ],
            'teaser' => 'sometimes',
            'goal_id' => [
                'required_with:teaser',
                'exists:goals,id'
            ]
        ]);

        if($validator->fails()){
            return response()->json([$validator->errors()], 400);
        }

        $goals = $broadcast->goals()->pluck('goals.id');
        $users = $broadcast->users()->pluck('users.id');

        if($request->has('media')){
            BroadcastUtils::broadcastMediaMessage(
                $goals->toArray(),
                $users->toArray(),
                $request->message,
                $user,
                $broadcast,
                $request->media,
                $request->type
            );
        } elseif ($request->has('teaser')) {
            BroadcastUtils::broadcastTeaserMessage(
                $goals->toArray(),
                $users->toArray(),
                $request->message,
                $user,
                $broadcast,
                $request->goal_id,
            );
        } else {
            BroadcastUtils::broadcastMessage(
                $goals->toArray(),
                $users->toArray(),
                $request->message,
                $user,
                $broadcast
            );
        }


        $response = BroadcastResource::compute($request,$broadcast)->first();
        return response()->json($response);
    }
}
