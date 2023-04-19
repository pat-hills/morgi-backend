<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Logger\Logger;
use App\Models\User;
use App\Services\Mailer\Mailer;
use App\Utils\EmailBlacklist\EmailBlacklistUtils;
use App\Utils\User\Auth\AuthUtils;
use App\Utils\User\Signup\SignupUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteAuth extends Controller
{
    public function facebookSignup(Request $request): JsonResponse
    {
        try {
            $facebook_user = Socialite::driver('facebook')->userFromToken($request->accessToken);
        } catch (\Exception $exception) {
            Logger::logException($exception);
            return response()->json([
                'message' => "Unable to authenticate via Facebook, Please try later!",
                'error' => $exception->getMessage()
            ], 500);
        }

        if(!isset($facebook_user->email, $facebook_user->id)){
            return response()->json(['message' => "Your Facebook account is invalid or not verified!"], 400);
        }

        if(EmailBlacklistUtils::set($facebook_user->email)->isBlacklisted()){
            return response()->json(['message' => "Your Facebook email is not eligible to signup!"], 400);
        }

        /*
         * Check if user is already registered as rookie or as leader.
         * If the registered user is a leader, he needs to send the password to attach the social account to morgi's account
         */
        $user = User::where('facebook_id', $facebook_user->id)->where('email', $facebook_user->email)->first();
        if(isset($user)){
            return response()->json(AuthUtils::login($request, $user, 'facebook'));
        }

        $user_by_email = User::query()->where('email', $facebook_user->email)->first();
        if(isset($user_by_email)){

            if($user_by_email->type==='rookie'){
                return response()->json(['message' => "Your Facebook's email is already attached to a rookie account"], 400);
            }

            if($user_by_email->type==='leader' && !isset($user_by_email->facebook_id)){
                $user_by_email_response = UserResource::compute(
                    $request,
                    $user_by_email
                )->first();
                return response()->json($user_by_email_response, 302);
            }
        }

        $utils = new SignupUtils($request->duplicate([
            'email' => $facebook_user->email,
            'signup_source' => 'facebook',
            'type' => 'leader',
            'password' => bcrypt(
                Str::uuid() . uniqid('', true)
            )
        ]));

        /*
         * If email is not used by any user, create new user
         */
        DB::beginTransaction();
        try {

            $user = $utils->signup();
            $user->update([
                'facebook_id' => $facebook_user->id,
                'active' => $user->type==='leader',
                'status' => $status = ($user->type==='leader') ? 'accepted' : 'new',
                'email_verified_at' => now()
            ]);

            $user->createUserStatusHistory($status, 'SYSTEM', 'Email verified by Facebook');

            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            Logger::logException($exception);
            return response()->json([
                'message' => 'Unable to create your account, please try later!',
                'error' => $exception->getMessage()
            ], 500);
        }

        try {
            Mailer::create($user)->setMisc()->setTemplate('LEADER_SOCIAL_WELCOME')->sendAndCreateUserEmailSentRow(now()->addMinutes(5)->timestamp);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        return response()->json(AuthUtils::login($request, $user, 'facebook', 4, true));
    }

    public function facebookUserAttach(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'string']
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $facebook_user = Socialite::driver('facebook')->userFromToken($request->accessToken);
        } catch (\Exception $exception) {
            Logger::logException($exception);
            return response()->json([
                'message' => "Unable to authenticate via Facebook, Please try later!",
                'error' => $exception->getMessage()
            ], 500);
        }

        if(!isset($facebook_user->email, $facebook_user->id)){
            return response()->json(['message' => "Your Facebook account is invalid or not verified!"], 400);
        }

        if($user->email!==$facebook_user->email || !Hash::check($request->password, $user->password)){
            return response()->json(['message' => "Invalid password provided!"], 400);
        }

        $user->update([
            'facebook_id' => $facebook_user->id
        ]);

        return response()->json(AuthUtils::login($request, $user, 'facebook'));
    }

    public function googleSignup(Request $request): JsonResponse
    {
        try {
            $google_user = Socialite::driver('google')->userFromToken($request->access_token);
        } catch (\Exception $exception) {
            Logger::logException($exception);
            return response()->json([
                'message' => "Unable to authenticate via Google, Please try later!",
                'error' => $exception->getMessage()
            ], 500);
        }

        if(!isset($google_user->email, $google_user->id)){
            return response()->json(['message' => "Your Google account is invalid or not verified!"], 400);
        }

        if(EmailBlacklistUtils::set($google_user->email)->isBlacklisted()){
            return response()->json(['message' => "Your Google email is not eligible to signup!"], 400);
        }

        /*
         * Check if user is already registered as rookie or as leader.
         * If the registered user is a leader, he needs to send the password to attach the social account to morgi's account
         */
        $user = User::where('google_id', $google_user->id)->where('email', $google_user->email)->first();
        if(isset($user)){
            return response()->json(AuthUtils::login($request, $user, 'google'));
        }

        $user_by_email = User::query()->where('email', $google_user->email)->first();
        if(isset($user_by_email)){

            if($user_by_email->type==='rookie'){
                return response()->json(['message' => "Your Google's email is already attached to a rookie account"], 400);
            }

            if($user_by_email->type==='leader' && !isset($user_by_email->google_id)){
                $user_by_email_response = UserResource::compute(
                    $request,
                    $user_by_email
                )->first();
                return response()->json($user_by_email_response, 302);
            }
        }

        $utils = new SignupUtils($request->duplicate([
            'email' => $google_user->email,
            'signup_source' => 'google',
            'type' => 'leader',
            'password' => bcrypt(
                Str::uuid() . uniqid('', true)
            )
        ]));

        /*
         * If email is not used by any user, create new user
         */
        DB::beginTransaction();
        try {

            $user = $utils->signup();
            $user->update([
                'google_id' => $google_user->id,
                'active' => $user->type==='leader',
                'status' => $status = ($user->type==='leader') ? 'accepted' : 'new',
                'email_verified_at' => now()
            ]);

            $user->createUserStatusHistory($status, 'SYSTEM', 'Email verified by Google');

            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            Logger::logException($exception);
            return response()->json([
                'message' => 'Unable to create your account, please try later!',
                'error' => $exception->getMessage()
            ], 500);
        }

        try {
            Mailer::create($user)->setMisc()->setTemplate('LEADER_SOCIAL_WELCOME')->sendAndCreateUserEmailSentRow(now()->addMinutes(5)->timestamp);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        return response()->json(AuthUtils::login($request, $user, 'google', 4, true));
    }

    public function googleUserAttach(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'string']
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $google_user = Socialite::driver('google')->userFromToken($request->access_token);
        } catch (\Exception $exception) {
            Logger::logException($exception);
            return response()->json([
                'message' => "Unable to authenticate via Google, Please try later!",
                'error' => $exception->getMessage()
            ], 500);
        }

        if(!isset($google_user->email, $google_user->id)){
            return response()->json(['message' => "Your Google account is invalid or not verified!"], 400);
        }

        if($user->email!==$google_user->email || !Hash::check($request->password, $user->password)){
            return response()->json(['message' => "Invalid password provided!"], 400);
        }

        $user->update([
            'google_id' => $google_user->id
        ]);

        return response()->json(AuthUtils::login($request, $user, 'google'));
    }
}
