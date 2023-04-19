<?php

namespace App\Http\Controllers;

use App\Logger\Logger;
use App\Mixpanel\Events\ComputeEvent;
use App\Mixpanel\Events\EventCarouselSwipe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MixpanelController extends Controller
{
    public function storeEvent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => ['required', 'string'],
            'data' => ['sometimes', 'nullable'],
            'rookie_id' => ['sometimes', 'integer', 'exists:rookies,id'],
            'leader_id' => ['sometimes', 'integer', 'exists:leaders,id']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $frontend_data = $request->data;

        if(isset($request->rookie_id)){
            $frontend_data['Rookie Id'] = $request->rookie_id;
        }

        if(isset($request->leader_id)){
            $frontend_data['Leader Id'] = $request->leader_id;
        }

        $user = $request->user();
        if(!isset($frontend_data)){
            $frontend_data = [];
        }

        try {
            ComputeEvent::compute($request->type, $user->id, $frontend_data);
        }catch (\Exception $exception){
            Logger::logException($exception);
            return response()->json(['message' => "Unable to store event", 'error' => $exception->getMessage()], 400);
        }

        return response()->json([]);
    }

    public function storeEventCarouselSwipe(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'data' => ['sometimes', 'nullable'],
            'rookies_ids' => ['required', 'array'],
            'rookies_ids.*' => ['required', 'integer', 'exists:rookies,id'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = $request->user();
        $frontend_data = $request->data;
        $frontend_data['Leader Id'] = $user->id;

        foreach ($request->rookies_ids as $rookie_id) {

            $frontend_data['Rookie Id'] = $rookie_id;
            $class = new EventCarouselSwipe($user->id, $frontend_data);

            try {
                $class->store();
            }catch (\Exception $exception){
                return response()->json(['message' => $exception->getMessage()], 400);
            }
        }

        return response()->json([]);
    }
}
