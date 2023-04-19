<?php

namespace App\Http\Controllers;

use App\Models\ChannelReadTimetoken;
use App\Models\PubnubChannel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ChannelReadTimetokenController extends Controller
{
    public function updateOrCreate(Request $request, PubnubChannel $pubnubChannel): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'timetoken' => ['required', 'numeric']
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        $user = $request->user();

        $channel_belong_to_user = ($user->type === 'leader')
            ? $pubnubChannel->leader_id === $user->id
            : $pubnubChannel->rookie_id === $user->id;

        if(!$channel_belong_to_user){
            return response()->json(['message' => 'Operation not permitted'], 403);
        }

        $last_time_token = ChannelReadTimetoken::updateOrCreate([
            'user_id' => $user->id,
            'channel_id' => $pubnubChannel->id
        ], [
            'timetoken' => $request->timetoken
        ]);

        return response()->json($last_time_token, 201);
    }
}
