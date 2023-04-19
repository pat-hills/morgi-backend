<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ComplaintType;
use App\Models\PubnubChannel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class ComplaintController extends Controller
{
    public function store(Request $request, PubnubChannel $pubnubChannel)
    {
        $validator = Validator::make($request->all(), [
            'user_reported' => ['required', 'exists:users,id'],
            'message' => ['required'],
            'type_id' => ['required', 'exists:complaints_types,id'],
            'notes' => ['required', 'string']
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $complaints_attribute = $request->only('user_reported', 'type_id', 'notes');
        $complaints_attribute['reported_by'] = Auth::id();
        $message = (array)$request->message;

        if(isset($message['type']) && $message['type'] === 'text'){
            $message['type'] = 'message';
            $message['text'] = $message['message'] ?? $message['text'];
            unset($message['message']);
        }

        $complaints_attribute['message'] = json_encode($message);

        Complaint::query()->create($complaints_attribute);

        return response()->json(['message' => trans('chat.successfully_reported')]);
    }

    public function indexTypes()
    {
        $complaints = Cache::get('complaints');
        if (isset($complaints)) {
            return response()->json($complaints);
        }

        $complaints = ComplaintType::all();

        Cache::put('complaints', $complaints, 86400);

        return response()->json($complaints);
    }
}
