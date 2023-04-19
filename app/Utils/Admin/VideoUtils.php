<?php

namespace App\Utils\Admin;

use App\Enums\UserEnum;
use App\Enums\VideoHistoryEnum;
use App\Models\User;
use App\Models\Video;
use App\Models\VideoHistory;
use App\Utils\NotificationUtils;
use App\Utils\ReasonUtils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VideoUtils {

    public static function approve(VideoHistory $video_history, $notification = true): bool
    {
        if (!Auth::check() || Auth::user()->type !== UserEnum::TYPE_ADMIN) {
            throw new \Exception('Operation not permitted!', 403);
        }

        if(empty($video_history)){
            throw new \Exception('Video not found!', 404);
        }

        $user = User::find($video_history->user_id);

        DB::beginTransaction();

        try {

            $current_video = Video::query()
                ->where('user_id', $video_history->user_id)
                ->first();

            $video_history->update([
                'status' => VideoHistoryEnum::STATUS_APPROVED,
                'admin_id' => Auth::id(),
            ]);

            Video::create(
                $video_history->only([
                    'user_id',
                    'is_processed',
                    'path_location',
                    'title',
                    'content',
                    'tags'
                ])
            );

            if(!empty($current_video)){
                $current_video->delete();
            }

            DB::commit();

        } catch (\Exception $exception) {
            DB::rollBack();
            throw new \Exception("Couldn't update this Video. Details: " . $exception->getMessage());
        }

        if ($notification){
            NotificationUtils::sendNotification($user->id, 'rookie_video_approved', now());
        }

        return true;
    }

    public static function decline(VideoHistory $video_history, $reason, $notification = true): bool
    {
        if (!Auth::check() || Auth::user()->type !== UserEnum::TYPE_ADMIN) {
            throw new \Exception('Operation not permitted!', 403);
        }

        if(empty($video_history)){
            throw new \Exception('Video not found!', 404);
        }

        $user = User::find($video_history->user_id);

        DB::beginTransaction();

        try {

            $video_history->update([
                'status' => VideoHistoryEnum::STATUS_DECLINED,
                'decline_reason' => $reason,
                'admin_id' => Auth::id(),
            ]);

            DB::commit();

        } catch (\Exception $exception) {
            DB::rollBack();
            throw new \Exception("Couldn't update this Video. Details: " . $exception->getMessage());
        }


        if ($notification){

            $notification_reason = ReasonUtils::ALL_REASON[$reason] ?? $reason;
            NotificationUtils::sendNotification($user->id, 'rookie_video_declined', now(), [
                'reason' => $notification_reason
            ]);
        }

        return true;
    }

    public static function declineStoredVideo(Video $video, $reason, $notification = true): bool
    {
        if (!Auth::check() || Auth::user()->type !== UserEnum::TYPE_ADMIN) {
            throw new \Exception('Operation not permitted!', 403);
        }

        if(empty($video)){
            throw new \Exception('Video not found!', 404);
        }

        $user = User::find($video->user_id);

        DB::beginTransaction();

        try {

            VideoHistory::query()
                ->where('path_location', $video->path_location)
                ->latest()
                ->update([
                    'status' => VideoHistoryEnum::STATUS_DECLINED,
                    'decline_reason' => $reason,
                    'admin_id' => Auth::id()
                ]);

            $video->delete();

            DB::commit();

        } catch (\Exception $exception) {
            DB::rollBack();
            throw new \Exception("Couldn't update this Video. Details: " . $exception->getMessage());
        }


        if ($notification){

            $notification_reason = ReasonUtils::ALL_REASON[$reason] ?? $reason;
            NotificationUtils::sendNotification($user->id, 'rookie_video_declined', now(), [
                'reason' => $notification_reason
            ]);
        }

        return true;
    }
}
