<?php

namespace App\Http\Controllers\Admin\Api;

use App\Enums\PubnubMessageEnum;
use App\Http\Controllers\Controller;
use App\Models\PubnubChannel;
use App\Models\PubnubMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AdminApiChatController extends Controller
{
    public function getChatData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'leader_id' => ['required', 'integer'],
            'rookie_id' => ['required', 'integer']
        ]);

        if($validator->fails()){
            return response()->json(['message' => $validator->errors()->getMessages()], Response::HTTP_BAD_REQUEST);
        }

        try {
            $leader = User::query()
                ->where('id', $request->leader_id)
                ->where('type', 'leader')
                ->firstOrFail();
        }catch (ModelNotFoundException $exception){
            return response()->json(['message' => 'Leader not found'], Response::HTTP_NOT_FOUND);
        }

        try {
            $rookie = User::query()
                ->where('id', $request->rookie_id)
                ->where('type', 'rookie')
                ->firstOrFail();
        }catch (ModelNotFoundException $exception){
            return response()->json(['message' => 'Rookie not found'], Response::HTTP_NOT_FOUND);
        }

        try {
            $pubnub_channel = PubnubChannel::query()
                ->where('rookie_id', $rookie->id)
                ->where('leader_id', $leader->id)
                ->firstOrFail();
        }catch (ModelNotFoundException $exception){
            return response()->json(['message' => 'Chat not found'],Response::HTTP_NOT_FOUND);
        }

        $messages = PubnubMessage::query()
            ->whereNotIn('type', [
                PubnubMessageEnum::TYPE_SYSTEM_BROADCAST,
                PubnubMessageEnum::TYPE_MICROMORGI_TRANSACTION,
                PubnubMessageEnum::TYPE_SUBSCRIPTION
            ])
            ->where('channel_id', $pubnub_channel->id)
            ->whereIn('sender_id', [$rookie->id, $leader->id])
            ->get();

        $response = [
            'is_leader_sent_message' => (isset($leader))
                ? $messages->where('sender_id', $leader->id)->isNotEmpty()
                : null,
            'is_rookie_sent_message' => (isset($rookie))
                ? $messages->where('sender_id', $rookie->id)->isNotEmpty()
                : null,
        ];

        return response()->json($response);
    }
}
