<?php

namespace App\Http\Controllers;

use App\Http\Resources\PhotoResource;
use App\Http\Resources\UserResource;
use App\Logger\Logger;
use App\Mixpanel\Events\EventDisconnectTelegramSuccess;
use App\Models\Photo;
use App\Models\User;
use App\Rules\PhotoValidation;
use App\Telegram\TelegramUtils;
use App\Utils\StorageUtils;
use App\Utils\User\Signup\SignupUtils;
use App\Utils\User\Signup\ValidationRulesUtils;
use App\Utils\User\Update\UserUpdateUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function signup(Request $request)
    {
        /*
         * Validation
         */
        $validation_rules = ValidationRulesUtils::getRules($request);
        $validation_custom_error_messages = ValidationRulesUtils::getCustomErrorMessages();

        $validator = Validator::make($request->all(), $validation_rules, $validation_custom_error_messages);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $utils = new SignupUtils($request);
        try {
            $utils->validate();
        }catch (\Exception $exception){
            return response()->json([
                'message' => $exception->getMessage()
            ], 400);
        }

        /*
         * Create user
         */
        DB::beginTransaction();
        try {
            $user = $utils->signup();
            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            Logger::logException($exception);
            return response()->json([
                'message' => 'Unable to create your account, please try later!',
                'error' => $exception->getMessage()
            ], 500);
        }

        $response = UserResource::compute(
            $request,
            $user,
            'own'
        )->first();

        return response()->json($response, 201);
    }

    public function update(Request $request)
    {
        if(isset($request->username)){
            $request->merge(['username' => (string)preg_replace('/[^A-Za-z0-9_.]/', '', $request->username)]);
        }

        /*
         * Validation
         */
        $validation_rules = \App\Utils\User\Update\ValidationRulesUtils::getRules($request);
        $validation_custom_error_messages = \App\Utils\User\Update\ValidationRulesUtils::getCustomErrorMessages(); 

        $validator = Validator::make($request->all(), $validation_rules, $validation_custom_error_messages);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        /*
         * Update user
         */
        $utils = new UserUpdateUtils($request);

        DB::beginTransaction();
        try {
            $user = $utils->update();
            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            Logger::logException($exception);
            return response()->json([
                'message' => $exception->getMessage()
            ], 400);
        }

        $response = UserResource::compute(
            $request,
            $user,
            'own'
        )->first();

        return response()->json($response);
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->user()->removeUserData();
            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            Logger::logException($exception);
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], 400);
        }

        return response()->json(['message' => trans('auth.account_deleted')]);
    }

    public function unsubscribe(string $token)
    {
        $user = User::where('unsubscribe_token', $token)->first();
        if(!isset($user)){
            return response()->json(['message' => trans('auth.invalid_token')], 404);
        }

        DB::beginTransaction();
        try {
            $user->removeUserData();
            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            Logger::logException($exception);
            return response()->json(['message' => trans('auth.invalid_token'), 'error' => $exception->getMessage()], 500);
        }

        return response()->json(['message' => trans('auth.account_unsubscribed')]);
    }

    public function getRandomAvatars(Request $request)
    {
        $user = $request->user();
        $avatars_num = 6;

        $field_to_find = $user->type . '_id';
        $field_to_join = ($user->type==='rookie') ? 'leader_id' : 'rookie_id';
        $other_type = ($user->type==='rookie') ? 'leader' : 'rookie';

        $is_gifted = true;
        $avatars = Photo::query()->select('photos.*')
            ->join('transactions', 'photos.user_id', '=', $field_to_join)
            ->whereNull('transactions.refund_type')
            ->whereIn('transactions.type', ['gift', 'chat'])
            ->where("transactions.$field_to_find", $user->id)
            ->groupBy('photos.user_id')
            ->inRandomOrder()
            ->limit($avatars_num)
            ->get();

        if($avatars->count() < $avatars_num){
            $is_gifted = false;
            $avatars = Photo::query()->select('photos.*')
                ->join('users', 'users.id', '=', 'photos.user_id')
                ->where('users.type', $other_type)
                ->groupBy('user_id')
                ->inRandomOrder()
                ->limit($avatars_num)
                ->get();
        }

        $response = PhotoResource::compute(
            $request,
            $avatars
        )->get();

        return response()->json([
            'is_gifted' => $is_gifted,
            'data' => $response
        ]);
    }

    public function signupAttempt(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:16', 'min:3']
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()], 400);
        }

        $username_available = !User::where('username', $request->username)->exists();

        // TODO: ritornare is_valid, cazzo
        return response()->json([
            'username' => $username_available,
        ]);
    }

    public function disconnectTelegramBot(Request $request)
    {
        $user = $request->user();
        if(!isset($user->joined_telegram_bot_at)){
            return response()->json(['message' => "You are not connected to the bot"], 400);
        }

        TelegramUtils::sendTelegramNotifications($user->telegram_chat_id, 'disconnect', null, $user->id);

        $user->unsubscribeFromTelegram();

        return response()->json([]);
    }

    public function discordAuth(Request $request)
    {
        $email = $request->email;
        if(!isset($email)){
            return response()->json(['is_valid' => false]);
        }

        $user_exists = User::query()
            ->where('type', '!=', 'admin')
            ->where('email', $email)
            ->exists();

        return response()->json(['is_valid' => $user_exists]);
    }

    public function setAvatar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'path_location' => ['required', new PhotoValidation()]
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = $request->user();

        $user->removeAvatar();
        $response = StorageUtils::assignObject($request->path_location, 'photo', $user);

        if($response['status']==='error'){
            return response()->json(['message' => $response['message']], 400);
        }

        try {
            $user->addPhoto($response['path_location'], true);
        }catch (\Exception $exception){
            return response()->json(['message' => $exception->getMessage()], 400);
        }

        $response = UserResource::compute(
            $request,
            $user,
            'own'
        )->first();

        return response()->json($response);
    }

    public function removeAvatar(Request $request)
    {
        $user = $request->user();
        $user->removeAvatar();

        $response = UserResource::compute(
            $request,
            $user,
            'own'
        )->first();

        return response()->json($response);
    }
}
