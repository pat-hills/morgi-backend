<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaderCcbillData;
use App\Models\LeaderPayment;
use App\Models\Photo;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserLoginHistory;
use App\Utils\Admin\UserRelatedAccountUtils;
use Illuminate\Http\JsonResponse;

class AdminApiUserRelatedAccountController extends Controller
{

    public function getLeaderRelatedAccount(User $user): JsonResponse
    {
        try {
            $result = UserRelatedAccountUtils::getLeaderRelatedAccount($user);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }

        $response = [
            'user' => $result['user'],
            'matched_users' => $result['matched_users'],
            'user_signup' => $result['user']->signup_login,
            'user_latest' => $result['user']->latest_login
        ];

        return response()->json($response);
    }

    public function getRookieRelatedAccount(User $user): JsonResponse
    {
        try {
            $result = UserRelatedAccountUtils::getRookieRelatedAccount($user);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }

        $response = [
            'user' => $result['user'],
            'matched_users' => $result['matched_users'],
            'user_signup' => $result['user']->signup_login,
            'user_latest' => $result['user']->latest_login
        ];

        return response()->json($response);
    }
}
