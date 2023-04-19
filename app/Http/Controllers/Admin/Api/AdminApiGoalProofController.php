<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Models\GoalProof;
use App\Models\User;
use App\Utils\Admin\AdminGoalUtils;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class AdminApiGoalProofController extends Controller
{
    public function approveProof(Request $request, Goal $goal, GoalProof $goal_proof): JsonResponse
    {
        $user = $request->user();

        try {
            AdminGoalUtils::approveProof($goal, $goal_proof, $user);
        }catch (\Exception $exception){
            return response()->json(['message' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return response()->json($goal_proof);
    }

    public function declineProof(Request $request, Goal $goal, GoalProof $goal_proof): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'decline_reason' => ['required']
        ]);

        if($validator->fails()){
            return response()->json(['message' => $validator->errors()->first()], Response::HTTP_BAD_REQUEST);
        }

        $user = $request->user();

        try {
            AdminGoalUtils::declineProof($goal, $goal_proof, $user, $request->decline_reason);
        }catch (\Exception $exception){
            return response()->json(['message' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return response()->json($goal_proof);
    }
}
