<?php

namespace App\Http\Controllers;

use App\Logger\Logger;
use App\Models\PasswordReset;
use App\Models\User;
use App\Models\UserFailedLogin;
use App\Utils\Utils;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PasswordResetController extends Controller {

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => ['required', 'string', 'email', 'exists:users,email']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::where('email', $request->email)->first();
        if ($user->status === 'deleted'){
            return response()->json(['message' => trans('auth.account_not_active')], 403);
        }

        try {
            $user->createPasswordReset(Utils::getRealIp($request));
        }catch (\Exception $exception){
            Logger::logException($exception);
            return response()->json(['message' => "Internal server error, try later"], 500);
        }

        return response()->json(['message' => trans('auth.password_recovery_email_sent')]);
    }

    public function find(string $token)
    {
        $password_reset = PasswordReset::where('token', $token)->first();
        if (!$password_reset) {
            return response()->json(['message' => trans('auth.invalid_token_or_input')], 404);
        }

        if (Carbon::parse($password_reset->updated_at)->addMinutes(720)->isPast()) {
            $password_reset->delete();
            return response()->json(['message' => 'Token expired'], 400);
        }

        return response()->json($password_reset);
    }

    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => ['required'],
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $password_reset = PasswordReset::where([
            ['token', $request->token],
            ['email', $request->email]
        ])->first();

        if (!$password_reset) {
            return response()->json(['message' => trans('auth.invalid_token_or_input')], 400);
        }

        if (Carbon::parse($password_reset->updated_at)->addMinutes(720)->isPast()) {
            $password_reset->delete();
            return response()->json(['message' => 'Token expired'], 400);
        }

        $user = User::where('email', $password_reset->email)->first();
        if (!isset($user)) {
            return response()->json(['message' => trans('auth.invalid_token_or_input')], 404);
        }

        if (Hash::check($request->password, $user->password)) {
            return response()->json(['message' => "Your password was not updated, since the provided password is your active password."], 400);
        }

        UserFailedLogin::where('user_id', $user->id)->where('password_forced', false)->update(['password_forced' => true]);
        $user->update([
            'password' => bcrypt($request->password),
            'password_forced' => false
        ]);
        $password_reset->delete();

        return response()->json(['message' => trans('auth.successfully_password_changed')]);
    }
}
