<?php

namespace App\Http\Controllers;

use App\Logger\Logger;
use App\Models\PubnubChannel;
use App\Models\PubnubMessage;
use App\Services\Chat\MessageMapper;
use App\Utils\PubnubMessageUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PubnubMessagesController extends Controller
{
    public function store(Request $request, PubnubChannel $pubnubChannel)
    {
        $validator = Validator::make($request->all(), [
            'message' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $requesting_user = $request->user();

        $message = MessageMapper::config($request->message, $pubnubChannel, 'Y-m-d H:i:s')->toObject();
        PubnubMessage::query()->create([
            'type' => $message->type,
            'sender_id' => $message->sender_id,
            'receiver_id' => $message->receiver_id,
            'channel_id' => $pubnubChannel->id,
            'sent_at' => $message->sent_at
        ]);

        $rookie_id = ($requesting_user->type === 'rookie')
            ? $message->sender_id
            : $message->receiver_id;

        $leader_id = ($requesting_user->type === 'leader')
            ? $message->sender_id
            : $message->receiver_id;

        $update_fields = [
            'last_activity_at' => $message->sent_at
        ];

        /*
         * Store first message at
         */
        $field_to_update = ($message->sender_id === $pubnubChannel->rookie_id)
            ? 'rookie_first_message_at'
            : 'leader_first_message_at';

        if(!isset($pubnubChannel->$field_to_update)){
            $update_fields[$field_to_update] = $message->sent_at;
        }

        $pubnubChannel->update($update_fields);

        /*
         * Send telegram notifications
         */
        if ($message->type !== 'micromorgi_transaction') {
            PubnubMessageUtils::sendTelegramNotifications($pubnubChannel, $message);
        }

        /*
         * Send ping email to leader
         */
        if($message->sender_id === $rookie_id){
            try {
                PubnubMessageUtils::sendLeaderPingEmail($pubnubChannel, $leader_id, $rookie_id);
            }catch (\Exception $exception){
                Logger::logException($exception);
            }
        }

        /*
         * If sender is leader, unset leader_received_ping_email_at
         * From now he can receive another email ping from rookie's message
         */
        if($requesting_user->type === 'leader'){
            $pubnubChannel->update([
                'leader_received_ping_email_at' => null
            ]);
        }

        /*
         * Message system complaint
         */
        try {
            PubnubMessageUtils::checkSystemComplaint($message);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        /*
         * Average response time
         */
        $avg_response_time = PubnubMessageUtils::getAvgResponseTime($pubnubChannel);

        if (isset($avg_response_time)) {
            $pubnubChannel->update([
                'avg_response_time' => $avg_response_time
            ]);
        }

        return response()->json($message);
    }
}
