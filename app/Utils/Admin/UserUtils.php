<?php

namespace App\Utils\Admin;

use App\Models\PhotoHistory;
use App\Models\User;
use App\Models\UserDescriptionHistory;
use App\Models\VideoHistory;
use App\Utils\NotificationUtils;
use App\Utils\ReasonUtils;

class UserUtils {

    /*
     * Photos, Video, Description
     */
    public static function approveAllUpdates(User $user, $notification = true){
        $updates = $user->updated_fields;

        if ($updates['bio']) {

            try {

                $new_description = UserDescriptionHistory::query()
                    ->where('user_id', $user->id)
                    ->orderBy('id', 'DESC')
                    ->first();

                DescriptionUtils::approve($new_description, $notification);

            } catch (\Exception $exception) {
                throw new \Exception($exception->getMessage());
            }
        }

        if ($updates['photo']) {

            $photo_uploaded = PhotoHistory::query()
                ->where('user_id', $user->id)
                ->where('status', '=', 'to_check')
                ->get();

            try {

                PhotoUtils::bulkApprove($photo_uploaded, $notification);
            } catch (\Exception $exception) {
                throw new \Exception($exception->getMessage());
            }
        }

        if ($updates['video']) {

            try {

                $video_uploaded = VideoHistory::query()
                    ->where('user_id', $user->id)
                    ->where('status', '=', 'to_check')
                    ->latest()
                    ->first();

                VideoUtils::approve($video_uploaded, $notification);
            } catch (\Exception $exception) {
                throw new \Exception($exception->getMessage());
            }
        }

        return true;
    }

    /*
     * Photos, Video, Description
     */
    public static function declineAllUpdates(User $user, $reason, $notification = true)
    {
        $updates = $user->updated_fields;
        $notification_counter = 0;

        if ($updates['bio']) {

            try {

                $new_description = UserDescriptionHistory::query()
                    ->where('user_id', $user->id)
                    ->orderBy('id', 'DESC')
                    ->first();

                DescriptionUtils::decline($new_description, $reason, false);
                $notification_counter++;

            } catch (\Exception $exception) {
                throw new \Exception($exception->getMessage());
            }
        }

        if ($updates['photo']) {

            $photo_uploaded = PhotoHistory::query()
                ->where('user_id', $user->id)
                ->where('status', '=', 'to_check')
                ->get();

            try {

                PhotoUtils::bulkDecline($photo_uploaded, $reason, false);
                $notification_counter++;

            } catch (\Exception $exception) {
                throw new \Exception($exception->getMessage());
            }
        }

        if ($updates['video']) {

            try {

                $video_uploaded = VideoHistory::query()
                    ->where('user_id', $user->id)
                    ->where('status', '=', 'to_check')
                    ->latest()
                    ->first();

                VideoUtils::decline($video_uploaded, $reason, false);
                $notification_counter++;

            } catch (\Exception $exception) {
                throw new \Exception($exception->getMessage());
            }
        }

        if($notification && $notification_counter > 0){

            $notification_reason = ReasonUtils::ALL_REASON[$reason] ?? $reason;
            NotificationUtils::sendNotification($user->id, 'updates_rejected', now(), [
                'reason' => $notification_reason
            ]);
        }

        return true;
    }
}
