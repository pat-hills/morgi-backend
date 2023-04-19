<?php

namespace App\Mixpanel\Events;

class ComputeEvent
{
    public static function compute(string $type, int $user_id, array $frontend_data): void
    {
        switch ($type){
            case 'gift_micromorgis_click':
                $class = new EventGiftMicromorgiClick($user_id, $frontend_data);
                break;
            case 'open_connection_click':
                $class = new EventOpenConnectionClick($user_id, $frontend_data);
                break;
            case 'open_connection_success':
                $class = new EventOpenConnectionSuccess($user_id, $frontend_data);
                break;
            case 'sent_message_success':
                $class = new EventSentMessageSuccess($user_id, $frontend_data);
                break;
            case 'sent_media_photo_success':
                $class = new EventSentPhotoSuccess($user_id, $frontend_data);
                break;
            case 'sent_media_video_success':
                $class = new EventSentVideoSuccess($user_id, $frontend_data);
                break;
            case 'buy_micromorgis_click':
                $class = new EventBuyMicromorgiClick($user_id, $frontend_data);
                break;
            case 'pause_connection_click':
                $class = new EventPauseConnectionClick($user_id, $frontend_data);
                break;
            case 'paused_connection_success':
                $class = new EventPausedConnectionSuccess($user_id, $frontend_data);
                break;
            case 'block_user_click':
                $class = new EventBlockUserClick($user_id, $frontend_data);
                break;
            case 'block_user_success':
                $class = new EventBlockUserSuccess($user_id, $frontend_data);
                break;
            case 'gift_morgi_click':
                $class = new EventGiftMorgiClick($user_id, $frontend_data);
                break;
            case 'leader_received_link':
                $class = new EventLeaderReceivedLink($user_id, $frontend_data);
                break;
            case 'carousel_swipe':
                $class = new EventCarouselSwipe($user_id, $frontend_data);
                break;
            default:
                throw new \Exception("Unable to retrieve the event {$type}");
        }

        try {
            $class->store();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }
}
