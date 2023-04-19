<?php

namespace App\Http\Controllers;

use App\Enums\UserEnum;
use App\Models\Country;
use App\Models\Gender;
use App\Models\Path;
use App\Models\User;
use App\Models\UserABGroup;
use App\Utils\User\UserTestUtils;
use Carbon\Carbon;
use Faker\Generator;
use Illuminate\Container\Container;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TestUserController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => ['required', Rule::in([
                UserEnum::TYPE_ROOKIE,
                UserEnum::TYPE_LEADER
            ])]
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }

        $user_credentials = UserTestUtils::create($request->type);

        return response()->json([
            'email' => $user_credentials['email'],
            'password' => $user_credentials['password']
        ]);
    }
}
