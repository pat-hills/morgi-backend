<?php

namespace App\Utils;

use App\Enums\PubnubChannelSettingEnum;
use App\Models\Subscription;
use App\Models\User;

class PubnubChannelUtils
{
    public static function isChatAttachmentsBlurred(string $channel_setting_type,
                                                    User $user,
                                                    User $channel_user,
                                                    array $converter_ids,
                                                    Subscription $subscription = null): bool
    {
        if($user->type === 'rookie'){
            return false;
        }

        switch ($channel_setting_type){
            case PubnubChannelSettingEnum::TYPE_ALL:
                $is_chat_attachments_blurred = true;
                break;
            case PubnubChannelSettingEnum::TYPE_CONVERTERS_ONLY:
                $is_chat_attachments_blurred = $channel_user->type === 'rookie' && in_array($channel_user->id, $converter_ids, true);
                break;
            case PubnubChannelSettingEnum::TYPE_AB:
                $is_chat_attachments_blurred = $user->id % 2 === 0;
                break;
            case PubnubChannelSettingEnum::TYPE_NONE:
            default:
                $is_chat_attachments_blurred = false;
                break;
        }

        if(isset($subscription)){
            if($subscription->status === 'active'){
                $is_chat_attachments_blurred = false;
            }

            if($subscription->status === 'canceled' && isset($subscription->valid_until_at) && strtotime($subscription->valid_until_at) >= now()->timestamp){
                $is_chat_attachments_blurred = false;
            }
        }

        return $is_chat_attachments_blurred;
    }
}
