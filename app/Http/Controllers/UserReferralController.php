<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Logger\Logger;
use App\Models\Rookie;
use App\Models\SmsSent;
use App\Models\User;
use App\Models\UserReferralEmailsSent;
use App\Rules\EmailValidation;
use App\Services\Mailer\Mailer;
use App\Services\Sms\Sms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserReferralController extends Controller
{
    public function referLeader(Request $request)
    {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', new EmailValidation()]
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $email = $request->email;
        if (User::where('email', $email)->exists()) {
            return response()->json(["message" => "Email belong to user already registered"], 400);
        }

        $rookie = Rookie::find($user->id);
        if(UserReferralEmailsSent::where('email', $email)->where('user_id', $rookie->id)->exists()) {
            return response()->json(["message" => "Referral already sent to that email"], 400);
        }

        $misc = [
            'refer_link' => env('LEADER_SIGNUP_FRONTEND_URL') . "?r={$user->username}",
            'name' => $rookie->full_name
        ];

        DB::beginTransaction();
        try {
            Mailer::create(null, $email)->setMisc($misc)->setTemplate('REFERRAL')->send();
            UserReferralEmailsSent::create([
                'user_id' => $rookie->id,
                'email' => $email
            ]);
            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            Logger::logException($exception);
            return response()->json(["message" => "Error during the email's delivery"], 400);
        }

        return response()->json(["message" => "Email successfully delivered!"]);
    }

    public function referRookie(Request $request)
    {
        $requesting_user = $request->user();
        $validator = Validator::make($request->all(), [
            'rookie_name' => ['required', 'string', 'min:3', 'max:24'],
            'leader_name' => ['required', 'string', 'min:3', 'max:24'],
            'telephone' => ['sometimes', 'string', 'min:6', 'max:14'],
            'email' => ['sometimes', 'string', 'email', new EmailValidation()]
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $refer_link = env('ROOKIE_SIGNUP_FRONTEND_URL') . "?r={$requesting_user->username}";

        if(isset($request->email)){

            $user = User::query()->where('email', $request->email)
                ->where('active', true)
                ->where('type', 'rookie')
                ->first();
            if(isset($user)){
                $rookie = UserResource::compute(
                    $request,
                    $user
                )->first();
                return response()->json($rookie, 302);
            }

            $already_referred = UserReferralEmailsSent::query()
                ->where('user_id', $requesting_user->id)
                ->where('email', $request->email)
                ->exists();
            if($already_referred){
                return response()->json(['message' => "You already referred this rookie!"], 400);
            }

            DB::beginTransaction();
            try {

                $misc = [
                    'rookie_name' => $request->rookie_name,
                    'leader_name' => $request->leader_name,
                    'refer_link' => $refer_link,
                    'leader_avatar' => $requesting_user->getOwnAvatar()->url ?? null,
                    'user_initials' => strtoupper(substr($requesting_user->username, 0, 2)),
                    'user_hex_bg' => $request->hex_bg
                ];

                Mailer::create(null, $request->email)->setMisc($misc)->setTemplate('LEADER_REFER_ROOKIE')->send();
                UserReferralEmailsSent::create([
                    'user_id' => $requesting_user->id,
                    'email' => $request->email
                ]);

                DB::commit();
            }catch (\Exception $exception){
                DB::rollBack();
                Logger::logException($exception);
                return response()->json(['message' => "Unable to send email"], 500);
            }

            return response()->json([]);
        }

        if(isset($request->telephone)){

            $already_referred = SmsSent::query()
                ->where('user_id', $requesting_user->id)
                ->where('telephone', $request->telephone)
                ->where('is_sent', true)
                ->exists();
            if($already_referred){
                return response()->json(['message' => "You already referred this rookie!"], 400);
            }

            $message = "{$request->leader_name}, who you know, is inviting you to Morgi to receive MOnthly Recurring GIfts of cash, mentorship and advice! Join now to start receiving support from {$request->leader_name} -> $refer_link";

            try {
                Sms::send(
                    $requesting_user->id,
                    $request->telephone,
                    $message
                );
            }catch (\Exception $exception){
                Logger::logException($exception);
                return response()->json(['message' => "Unable to send SMS"], 500);
            }

            return response()->json([]);
        }

        return response()->json(['message' => "You must send telephone or email!"], 400);
    }
}
