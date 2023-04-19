<?php

namespace App\Utils\Admin;

use App\Enums\UserDescriptionHistoryEnum;
use App\Enums\UserEnum;
use App\Models\User;
use App\Models\UserDescriptionHistory;
use App\Utils\NotificationUtils;
use App\Utils\ReasonUtils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DescriptionUtils {

    public static function approve(UserDescriptionHistory $description_history, $notification = true): bool
    {
        if (!Auth::check() || Auth::user()->type !== UserEnum::TYPE_ADMIN) {
            throw new \Exception('Operation not permitted!', 403);
        }

        if(empty($description_history)){
            throw new \Exception('Description not found!', 404);
        }

        if($description_history->status !== UserDescriptionHistoryEnum::STATUS_TO_CHECK){
            throw new \Exception('Something went wrong. Please contact support team!', 400);
        }

        $user = User::find($description_history->user_id);

        DB::beginTransaction();
        try {

            $description_history->update([
                'status' => UserDescriptionHistoryEnum::STATUS_APPROVED,
                'admin_id' => Auth::id(),
            ]);

            $user->update([
                'description' => $description_history->description
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new \Exception("Couldn't update this description. Details: " . $exception->getMessage());
        }

        if ($notification){
            NotificationUtils::sendNotification($user->id, 'description_approved', now());
        }

        return true;
    }

    public static function decline(UserDescriptionHistory $description_history, $reason, $notification = true): bool
    {
        if (!Auth::check() || Auth::user()->type !== UserEnum::TYPE_ADMIN) {
            throw new \Exception('Operation not permitted!', 403);
        }

        if(empty($description_history)){
            throw new \Exception('Description not found!', 404);
        }

        if($description_history->status === UserDescriptionHistoryEnum::STATUS_DECLINED){
            throw new \Exception('Description already declined!', 404);
        }

        $user = User::find($description_history->user_id);

        DB::beginTransaction();

        try {

            $description_history->update([
                'status' => UserDescriptionHistoryEnum::STATUS_DECLINED,
                'decline_reason' => ReasonUtils::ALL_REASON[$reason] ?? $reason,
                'admin_id' => Auth::id(),
            ]);

            DB::commit();

        } catch (\Exception $exception) {
            DB::rollBack();
            throw new \Exception("Couldn't update this description. Details: " . $exception->getMessage());
        }

        if ($notification){

            $notification_reason = ReasonUtils::ALL_REASON[$reason] ?? $reason;
            NotificationUtils::sendNotification($user->id, 'description_declined', now(), [
                'reason' => $notification_reason
            ]);
        }

        return true;
    }

    public static function declineStoredDescription(User $user, $reason, $notification = true): bool
    {
        if (!Auth::check() || Auth::user()->type !== UserEnum::TYPE_ADMIN) {
            throw new \Exception('Operation not permitted!', 403);
        }

        if(empty($user)){
            throw new \Exception('User not found!', 404);
        }

        DB::beginTransaction();

        try {

            UserDescriptionHistory::query()
                ->where('user_id', $user->id)
                ->latest()
                ->first()
                ->update([
                    'status' => UserDescriptionHistoryEnum::STATUS_DECLINED,
                    'decline_reason' => ReasonUtils::ALL_REASON[$reason] ?? $reason,
                    'admin_id' => Auth::id(),
                ]);

            $user->update([
                'description' => null
            ]);

            DB::commit();

        } catch (\Exception $exception) {
            DB::rollBack();
            throw new \Exception("Couldn't update this description. Details: " . $exception->getMessage());
        }

        if ($notification){

            $notification_reason = ReasonUtils::ALL_REASON[$reason] ?? $reason;
            NotificationUtils::sendNotification($user->id, 'description_declined', now(), [
                'reason' => $notification_reason
            ]);
        }

        return true;
    }
}
