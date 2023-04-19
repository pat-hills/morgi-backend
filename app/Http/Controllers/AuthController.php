<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Logger\Logger;
use App\Mixpanel\Events\EventEmailVerifiedSuccess;
use App\Mixpanel\Events\EventLoginSuccess;
use App\Mixpanel\Events\EventLogoutSuccess;
use App\Mixpanel\Utils\UserProfileUtils;
use App\Models\User;
use App\Models\UserEmailSent;
use App\Models\UserFailedLogin;
use App\Services\Chat\Chat;
use App\Utils\NotificationUtils;
use App\Utils\User\Auth\AuthUtils;
use App\Utils\User\Signup\SignupUtils;
use App\Utils\Utils;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PubNub\Exceptions\PubNubException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember_me' => ['sometimes', 'boolean']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::where('email', $request->email)->first();
        if(!isset($user) || $user->status === 'deleted' || in_array($user->type, ['admin', 'operator'])){
            return response()->json(['message' => trans('auth.wrong_credentials')], 401);
        }

        if($user->status === 'fraud'){
            return response()->json(['message' => "Hello, your account has been blocked. For more information, contact Customer Support via the chat icon"], 400);
        }

        if($user->password_forced){
            return response()->json(['message' => "Please change password using the 'Forgot password?' link"], 403);
        }

        if(!Auth::attempt($request->only('email', 'password'))){

            UserFailedLogin::query()->create(['user_id' => $user->id, 'password_forced' => $user->password_forced]);

            $failed_logins_count = UserFailedLogin::query()
                ->where('user_id', $user->id)
                ->where('password_forced', false)
                ->whereDate('created_at', Carbon::today());

            if($failed_logins_count->count()>=3){

                DB::beginTransaction();
                try {
                    $user->update(['password_forced' => true]);
                    $failed_logins_count->update(['password_forced' => true]);
                    $user->createPasswordReset(Utils::getRealIp($request));
                    DB::commit();
                }catch (\Exception $exception){
                    DB::rollBack();
                    Logger::logException($exception);
                    return response()->json(['message' => trans('auth.wrong_credentials')], 401);
                }

                return response()->json(['message' => "Please change password using the 'Forgot password?' link"], 403);
            }

            return response()->json(['message' => trans('auth.wrong_credentials')], 401);
        }

        if(!$user->email_verified_at){
            if($user->emailsSentByTypeCount('ACCOUNT_ACTIVATION')<=1){
                SignupUtils::sendSignupActivateEmail($user);
                return response()->json(['message' => trans('auth.activation_email_resent')], 401);
            }

            $last_email_sent_date = UserEmailSent::where('user_id', $user->id)
                ->where('type', 'ACCOUNT_ACTIVATION')
                ->latest()
                ->first()
                ->created_at
                ->toDateString();

            $message = str_replace('{{date}}', $last_email_sent_date, trans('auth.email_not_confirmed'));

            return response()->json(['message' => $message], 403);
        }

        return response()->json(
            AuthUtils::login($request, $user, 'morgi')
        );
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        try {
            Chat::config($user->id)->logout($user, $request->bearerToken());
        }catch (PubNubException $exception){
            \App\Services\Chat\Utils::storeError($exception, 'logout');
        }

        try {
            EventLogoutSuccess::config($user->id);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        $request->user()->token()->revoke();

        return response()->json(['message' => trans('auth.successfully_logout')]);
    }

    public function user(Request $request)
    {
        $user = $request->user();

        try {
            UserProfileUtils::storeOrUpdate($user->id);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        $response = UserResource::compute(
            $request,
            $user,
            'own'
        )->first();

        return response()->json($response);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'password' => ['required', 'string', 'confirmed', 'min:6', 'max:32']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = $request->user();
        if (Hash::check($request->password, $user->password)) {
            return response()->json(['message' => "Your password was not updated, since the provided password is your active password."], 400);
        }

        $user->update([
            'password' => bcrypt($request->password)
        ]);

        return response()->json(['message' => trans('auth.successfully_password_changed')]);
    }

    public function checkCurrentPassword(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'password' => ['required', 'string']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = $request->user();

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => "Invalid password provided!"], 400);
        }

        return response()->json(['message' => trans('auth.successfully_password_changed')]);
    }

    public function signupActivate(Request $request, string $token)
    {
        $user = User::query()->where('activation_token', $token)->first();
        if(!isset($user)){
            return response()->json(['message' => trans('auth.invalid_token')], 400);
        }

        if(isset($user->email_verified_at)){
            return response()->json([
                'message' => trans('auth.already_active'),
                'user' => UserResource::compute(
                    $request,
                    $user,
                    'own'
                )->first()
            ]);
        }

        $user->active = $user->type==='leader';
        $status = ($user->type==='leader') ? 'accepted' : 'new';
        $user->createUserStatusHistory($status, 'SYSTEM', 'Email verified');
        $user->status = $status;
        $user->email_verified_at = now();
        $user->save();

        try {
            EventEmailVerifiedSuccess::config($user->id);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        $response = UserResource::compute(
            $request,
            $user,
            'own'
        )->first();

        return response()->json($response);
    }
}
