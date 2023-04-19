<?php


namespace App\Webhooks\ElasticTranscoder;


use App\Models\Video;
use App\Models\VideoHistory;
use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ElasticTranscoderWebhook
{
    public function store(Request $request): JsonResponse
    {
        try {

            $message = Message::fromRawPostData();
            $validator = new MessageValidator();

            if(!$validator->isValid($message)) {
                return response()->json(['message' => "Invalid payload provided"], 400);
            }

            $type = $message['Type'];

            if($type==='SubscriptionConfirmation'){
                file_get_contents($message['SubscribeURL']);
                return response()->json([]);
            }

            if($type==='Notification'){

                $body = (object)$message['Message'];
                $body = json_decode($body->scalar, false);

                if(strtolower($body->state)==='completed'){

                    $path_location = $body->input->key;
                    $video_history = VideoHistory::query()->where('path_location', $path_location)->first();

                    if(isset($video_history)){
                        $video_history->update(['is_processed' => true, 'path_location' => $body->outputKeyPrefix . $body->outputs[0]->key]);
                    }

                    $video = Video::query()->where('path_location', $path_location)->first();

                    if(isset($video)){
                        $video->update(['is_processed' => true, 'path_location' => $body->outputKeyPrefix . $body->outputs[0]->key]);
                    }

                    return response()->json($body);
                }

                if(strtolower($body->state)==='error'){
                    return response()->json([]);
                }

                return response()->json(['message' => "Invalid state provided"], 400);
            }

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

        return response()->json(['message' => "Invalid type provided"], 400);
    }
}
