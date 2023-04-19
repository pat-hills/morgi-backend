<?php

namespace App\Utils\Admin;

use App\Enums\PhotoHistoryEnum;
use App\Enums\UserEnum;
use App\FaceRecognition\AwsFaceRekognitionFacesUtils;
use App\FaceRecognition\AwsFaceRekognitionSearchUtils;
use App\Models\Photo;
use App\Models\PhotoHistory;
use App\Models\User;
use App\Orazio\OrazioHandler;
use App\Utils\NotificationUtils;
use App\Utils\ReasonUtils;
use App\Utils\Utils;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PhotoUtils {

    public static function approve(PhotoHistory $photo_history, $notification = true): bool
    {
        if (!Auth::check() || Auth::user()->type !== UserEnum::TYPE_ADMIN) {
            throw new \Exception('Operation not permitted!', 403);
        }

        if(empty($photo_history)){
            throw new \Exception('Photo not found!', 404);
        }

        $user = User::find($photo_history->user_id);

        DB::beginTransaction();

        try {

            $photo_history->update([
                'status' => PhotoHistoryEnum::STATUS_APPROVED,
                'admin_id' => Auth::id(),
            ]);

            $photo = Photo::create(
                $photo_history->only([
                    'user_id',
                    'path_location',
                    'main',
                    'title',
                    'content',
                    'tags'
                ])
            );

            if ($user->type === 'leader') {
                (new AwsFaceRekognitionSearchUtils())->matchLeaderRookies($photo);
            } else {
                (new AwsFaceRekognitionSearchUtils())->matchRookieRookies($photo);
            }

            (new AwsFaceRekognitionFacesUtils())->storePhotoFaces($photo);

            DB::commit();

        } catch (\Exception $exception) {
            DB::rollBack();
            throw new \Exception("Couldn't update this photo. Details: " . $exception->getMessage());
        }

        if($user->type==='leader'){
            try {
                OrazioHandler::freshSeen($user->id, 'Admin approved photo, new session for face recognition');
            }catch (\Exception $exception){
            }
        }

        if ($notification){
            NotificationUtils::sendNotification($user->id, 'photo_approved', now());
        }

        return true;
    }

    public static function bulkApprove(collection $photos_histories, $notification = true): bool
    {
        if (!Utils::validateCollection($photos_histories, PhotoHistory::class)){
            throw new \Exception("Passed wrong params", 400);
        }

        if (!Auth::check() || Auth::user()->type !== UserEnum::TYPE_ADMIN) {
            throw new \Exception('Operation not permitted!', 403);
        }

        $first_photo_history = $photos_histories->first();
        if(empty($first_photo_history)){
            throw new \Exception('Photo not found!', 404);
        }

        $user = User::find($photos_histories->first()->user_id);

        DB::beginTransaction();

        try {

            PhotoHistory::query()
                ->whereIn('id', $photos_histories->pluck('id'))
                ->update([
                    'status' => PhotoHistoryEnum::STATUS_APPROVED,
                    'admin_id' => Auth::id(),
                ]);

            foreach ($photos_histories as $photo_to_create){
                $photo = Photo::create(
                    $photo_to_create->only([
                        'user_id',
                        'path_location',
                        'main',
                        'title',
                        'content',
                        'tags'
                    ])
                );

                if ($user->type === 'leader') {
                    (new AwsFaceRekognitionSearchUtils())->matchLeaderRookies($photo);
                } else {
                    (new AwsFaceRekognitionSearchUtils())->matchRookieRookies($photo);
                }

                (new AwsFaceRekognitionFacesUtils())->storePhotoFaces($photo);
            }

            DB::commit();

        } catch (\Exception $exception) {
            DB::rollBack();
            throw new \Exception("Couldn't approve these photos. Details: " . $exception->getMessage());
        }

        if($user->type==='leader'){
            try {
                OrazioHandler::freshSeen($user->id, 'Admin approved photo, new session for face recognition');
            }catch (\Exception $exception){
            }
        }

        if ($notification) {

            $notification_type = 'photo_approved';
            if (!empty($photos_histories) && count($photos_histories) > 1) {
                $notification_type = 'photos_approved';
            }

            NotificationUtils::sendNotification($user->id, $notification_type, now());
        }

        return true;
    }

    public static function decline(PhotoHistory $photo_history, $reason, $notification = true): bool
    {
        if (!Auth::check() || Auth::user()->type !== UserEnum::TYPE_ADMIN) {
            throw new \Exception('Operation not permitted!', 403);
        }

        if(empty($photo_history)){
            throw new \Exception('Photo not found!', 404);
        }

        $user = User::find($photo_history->user_id);

        DB::beginTransaction();

        try {

            $photo_history->update([
                'status' => PhotoHistoryEnum::STATUS_DECLINED,
                'decline_reason' => $reason,
                'admin_id' => Auth::id(),
            ]);

            DB::commit();

        } catch (\Exception $exception) {
            DB::rollBack();
            throw new \Exception("Couldn't update this photo. Details: " . $exception->getMessage());
        }

        if ($notification){

            $notification_reason = ReasonUtils::ALL_REASON[$reason] ?? $reason;
            NotificationUtils::sendNotification($user->id, 'photo_declined', now(), [
                'reason' => $notification_reason
            ]);
        }

        return true;
    }

    public static function bulkDecline(collection $photos_histories, $reason, $notification = true): bool
    {
        if (!Utils::validateCollection($photos_histories, PhotoHistory::class)){
            throw new \Exception("Passed wrong params", 400);
        }

        if (!Auth::check() || Auth::user()->type !== UserEnum::TYPE_ADMIN) {
            throw new \Exception('Operation not permitted!', 403);
        }

        $first_photo_history = $photos_histories->first();
        if(empty($first_photo_history)){
            throw new \Exception('Photo not found!', 404);
        }

        $first_photo_history = $photos_histories->first();
        if(empty($first_photo_history)){
            throw new \Exception('Photo not found!', 404);
        }

        $user = User::find($photos_histories->first()->user_id);

        DB::beginTransaction();

        try {

            PhotoHistory::query()
                ->whereIn('id', $photos_histories->pluck('id'))
                ->update([
                    'status' => PhotoHistoryEnum::STATUS_DECLINED,
                    'decline_reason' => $reason,
                    'admin_id' => Auth::id(),
                ]);

            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            throw new \Exception("Couldn't update these photos. Details: " . $exception->getMessage());
        }

        if ($notification) {

            $notification_reason = ReasonUtils::ALL_REASON[$reason] ?? $reason;
            NotificationUtils::sendNotification($user->id, 'photo_declined', now(), [
                'reason' => $notification_reason
            ]);
        }

        return true;
    }

    public static function declineStoredPhoto(Photo $photo, $reason, $notification = true): bool
    {
        if (!Auth::check() || Auth::user()->type !== UserEnum::TYPE_ADMIN) {
            throw new \Exception('Operation not permitted!', 403);
        }

        if(empty($photo)){
            throw new \Exception('Photo not found!', 404);
        }

        DB::beginTransaction();

        try {

            PhotoHistory::query()
                ->where('user_id', $photo->user_id)
                ->where('path_location', $photo->path_location)
                ->latest()
                ->update([
                    'decline_reason' => $reason,
                    'admin_id' => Auth::id(),
                    'status' => PhotoHistoryEnum::STATUS_DECLINED
                ]);

            Photo::query()
                ->find($photo->id)
                ->delete();

            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            throw new \Exception("Couldn't delete the current photo", 400);
        }

        if ($notification) {

            $notification_reason = ReasonUtils::ALL_REASON[$reason] ?? $reason;
            NotificationUtils::sendNotification($photo->user_id, 'photo_declined', now(), [
                'reason' => $notification_reason
            ]);
        }

        return true;
    }
}
