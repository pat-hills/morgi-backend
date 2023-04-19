<?php

namespace App\Utils\User\Auth;

use App\Http\Resources\UserResource;
use App\Logger\Logger;
use App\Mixpanel\Events\EventLoginSuccess;
use App\Models\User;
use App\Utils\Utils;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AuthUtils
{
    public static function login(Request $request, User $user, string $source, int $duration_in_weeks = 4, bool $is_signup = false): array
    {
        $source = ucfirst(strtolower(trim($source)));

        $personal_access_token = $user->createToken("$source Access Token");
        $token = $personal_access_token->token;

        $token->expires_at = Carbon::now()->addWeeks($duration_in_weeks);
        $token->save();

        try {
            EventLoginSuccess::config($user->id, $source);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        $is_first_login = !isset($user->last_login_at);

        $user->newLogin(Utils::getRealIp($request), $request->header('User-Agent'), $is_signup);

        return [
            'access_token' => $personal_access_token->accessToken,
            'token_type' => 'Bearer',
            'expires_at' =>  Carbon::now()->addWeeks($duration_in_weeks)->toDateTimeString(),
            'is_first_login' => $is_first_login,
            'user' => UserResource::compute(
                $request,
                $user,
                'own'
            )->first()
        ];
    }
}
