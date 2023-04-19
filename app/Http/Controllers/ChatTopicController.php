<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatTopic; 
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; 
use App\Models\User; 
use App\Utils\User\Signup\SignupTopicUtils; 
use App\Http\Resources\ChatTopicResource;
use App\Utils\User\Signup\ValidationRulesUtils;
use Illuminate\Support\Facades\DB;

 

class ChatTopicController extends Controller
{

    public function store(Request $request)
    { 
        $validator = Validator::make($request->all(), [
            'name' => ['required','string']
        ]); 

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $chat_topic_name = ucfirst(strtolower(trim($request->name)));
        $chat_topic_key_name = str_replace(' ', '_', strtolower(trim($request->name)));

        $check_topic_name = ChatTopic::query()->where('key_name', $chat_topic_key_name)->exists();

        if($check_topic_name)
        {
            return response()->json(['message' => "Chat topic already exits"], 400);
        }

        $chat_topic = ChatTopic::create([
            'name' => $chat_topic_name,
            'key_name' => $chat_topic_key_name
        ]);

        $response = ChatTopicResource::compute(
            $request,
            $chat_topic
        )->first();

        return response()->json($response, 201); 

    }

    public function index(Request $request)
    {
        $chat_topics = ChatTopic::query();

        if(isset($request->sort_by, $request->sort_direction) && $request->sort_by === 'users_chat_topics_count'){
            $chat_topics = $chat_topics
            ->withCount('usersChatTopics')
            ->orderBy('users_chat_topics_count', 'desc');
        }

        $chat_topics = $chat_topics->get();

        $response = ChatTopicResource::compute(
            $request,
            $chat_topics
        )->get();

        return response()->json($response, 201);
    }
}
