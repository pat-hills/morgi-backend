<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GoalResource;
use App\Models\Goal;
use App\Models\User;
use App\Utils\Admin\AdminGoalUtils;
use App\Utils\DataTableUtils;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class AdminApiGoalController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        try {
            DataTableUtils::validate($request);
        }catch (\Exception $exception){
            return response()->json(['message' => $exception->getMessage()], $exception->getCode());
        }
        if(isset($request->search['value'])){
            $request->merge(['rookie_id' => $request->search['value']]);
        }

        $status = (isset($request->columns[3]['search']['value']))
            ? $request->columns[3]['search']['value']
            : null;

        $request->merge(['status' => $status]);

        $validator = Validator::make($request->all(), [
            'status' => ['sometimes', 'nullable', Rule::in(Goal::STATUS)],
            'rookie_id' => ['sometimes', 'nullable']
        ]);

        if($validator->fails()){
            return response()->json(['message' => $validator->errors()->getMessages()], Response::HTTP_BAD_REQUEST);
        }

        $dataTable = new DataTableUtils($request);

        $now = Carbon::now(config('app.timezone'))->toDateTimeString();

        $goal_query = Goal::query()
            ->select('goals.*')
            ->selectRaw("( CASE WHEN goals.status = 'proof_pending_approval' OR goals.status = 'pending' OR goals.status = 'review' THEN 1 ELSE 0 END) AS priority")
            ->selectRaw("( CASE WHEN goals.status = 'proof_pending_approval' OR goals.status = 'pending' OR goals.status = 'review' THEN TIMESTAMPDIFF(minute, updated_at, '$now') ELSE 0 END) AS time_waiting");

        if(isset($request->status)){
            $goal_query->where('status', $request->status);
        }

        if(isset($request->rookie_id)){
           $goal_query->where('rookie_id', $request->rookie_id);
        }

        if (isset($dataTable->sort_key)){

            switch (strtolower($dataTable->sort_key)){
                case 'time_waiting':
                    $goal_query->orderByRaw("priority {$dataTable->sort_direction}");
                    $goal_query->orderByRaw("time_waiting {$dataTable->sort_direction}");
                    break;
                case 'created_at':
                default:
                    $goal_query->orderBy('created_at', $dataTable->sort_direction);
                    break;
            }
        }

        $all_rows = $goal_query->count();

        $response = $dataTable->getResponse($goal_query, $all_rows);
        return response()->json($response);
    }

    public function show(Request $request, Goal $goal): JsonResponse
    {
        $response = GoalResource::compute($request, $goal, 'extended')->first();
        return response()->json($response);
    }

    public function updateStatus(Request $request, Goal $goal): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'action' => ['required', Rule::in(Goal::STATUS)],
            'decline_reason' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return AdminGoalUtils::isReasonRequired($request->action);
                }),
                'string'
            ],
        ]);

        if($validator->fails()){
            return response()->json(['message' => $validator->errors()->first()], Response::HTTP_BAD_REQUEST);
        }

        if($goal->status === Goal::STATUS_CANCELLED){
            return response()->json(['message' => 'Operation not permitted, goal cancelled'], Response::HTTP_FORBIDDEN);
        }

        $user = $request->user();

        try {
            AdminGoalUtils::updateStatus($goal, $user, $request->action, $request->decline_reason);
        }catch (\Exception $exception){
            return response()->json(['message' => 'Could not update this Goal, please try later', 'error' => $exception->getMessage()], $exception->getCode());
        }

        return response()->json($goal);
    }

    public function getGoalsStatusCounter(Request $request): JsonResponse
    {
        $status_counter = Goal::query()
            ->selectRaw('count(*) as counter, status')
            ->groupBy('status')
            ->get();
        return response()->json($status_counter);
    }

    public function getAllAvailableGoalStatusAction(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_status' => ['required', Rule::in(Goal::STATUS)],
        ]);

        if($validator->fails()){
            return response()->json(['message' => $validator->errors()->first()], Response::HTTP_BAD_REQUEST);
        }

        $all_status = Goal::AVAILABLE_STATUS_BY_STATUS[$request->current_status] ?? null;

        if(empty($all_status)){
            return response()->json(null);
        }

        $all_status = array_map(function($status){
            return [
                'name' => $status,
                'is_reason_required' => AdminGoalUtils::isReasonRequired($status)
            ];
        }, $all_status);
        return response()->json($all_status);
    }
}
