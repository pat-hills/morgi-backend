<?php

namespace App\Mixpanel\Utils;

use App\Mixpanel\Api\UserProfiles;
use App\Mixpanel\Models\LeaderProfile;
use App\Mixpanel\Models\RookieProfile;
use App\Models\User;

class UserProfileUtils
{
    public static function storeOrUpdate(int $user_id): void
    {
        $model = self::getUserModelArray($user_id);

        try {
            UserProfiles::config($user_id)->setProperties($model)->storeOrUpdate();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }

    public static function getUserModelArray(int $user_id): array
    {
        $user = User::find($user_id);
        $model = ($user->type === 'leader')
            ? LeaderProfile::config($user_id)
            : RookieProfile::config($user_id);

        return $model->toArray();
    }
}
