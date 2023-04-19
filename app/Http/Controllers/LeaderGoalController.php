<?php

namespace App\Http\Controllers;

use App\Http\Resources\GoalResource;
use App\LeaderPackages\SpendMicromorgi;
use App\Models\Broadcast;
use App\Models\BroadcastMessage;
use App\Models\Goal;
use App\Models\GoalDonation;
use App\Models\Path;
use App\Models\Leader;
use App\Models\PubnubChannel;
use App\Models\Rookie;
use App\Models\User;
use App\Models\UserBlock;
use App\Services\Chat\Chat;
use App\Transactions\MicroMorgi\TransactionGoal;
use App\Utils\BroadcastUtils;
use App\Utils\NotificationUtils;
use App\Utils\UserUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class LeaderGoalController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_by' => ['sometimes', Rule::in(['target_amount'])],
            'order_direction' => ['sometimes', Rule::in(['asc', 'desc'])],
            'time_range' => ['sometimes', 'integer', 'min:1'],
            'path_ids' => ['sometimes', 'array'],
            'path_ids.*' => ['required', 'integer'],
            'subpath_ids' => ['sometimes', 'array'],
            'subpath_ids.*' => ['required', 'integer'],
            'type_id' => ['sometimes', 'integer']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $requesting_user = $request->user();
        $blocked_ids = UserUtils::getUsersBlockIds($requesting_user->id);

        $goals = Goal::query()
            ->select('goals.*')
            ->whereNotIn('goals.rookie_id', $blocked_ids)
            ->whereActive();

        if ($request->has('order_by')){
            $goals = $goals->orderBy($request->order_by, strtoupper($request->order_direction));
        }else{
            $goals = $goals->withSum('successfulDonations', 'amount')->orderBy('successful_donations_sum_amount', 'DESC');
        }

        if ($request->has('time_range')){
            $goals = $goals->timeRangeInHours($request->time_range);
        }

        if (!empty($request->path_ids) || !empty($request->subpath_ids)){

            $goals = $goals->join('users_paths', 'goals.rookie_id', '=', 'users_paths.user_id');
            $paths_ids = [];
            if (!empty($request->path_ids)){
                $paths_ids = array_merge($paths_ids, $request->path_ids);
            }

            if (!empty($request->subpath_ids)){
                $paths_ids = array_merge($paths_ids, $request->subpath_ids);
            }

            $goals = $goals->whereIn('users_paths.path_id', $paths_ids);
        }

        if ($request->has('type_id')){
            $goals = $goals->where('type_id', $request->type_id);
        }

        $goals->groupBy('goals.id');
        $goals = $goals->paginate($request->get('limit', 15));

        $response = GoalResource::compute($request, $goals)->get();
        return response()->json($response);
    }

    public function show(Goal $goal, Request $request)
    {
        $request_user = $request->user();
        $requester_own_goal = isset($request_user) && $goal->rookie_id === $request_user->id;
        if($goal->status === Goal::STATUS_PENDING && !$requester_own_goal){
            return response()->json([], 404);
        }

        $response = GoalResource::compute($request, $goal)->first();
        return response()->json($response);
    }

    public function donate(Request $request, Rookie $rookie, Goal $goal)
    {
        if ($goal->rookie_id !== $rookie->id){
            return response()->json(["message" => "Goal not found"], 404);
        }

        if(!$goal->isActive()){
            return response()->json(["message" => "Goal not found"], 404);
        }

        if (!$rookie->active){
            return response()->json(['message' => trans('auth.account_not_active')], 400);
        }

        $leader = Leader::query()->find(Auth::id());

        if ($rookie->hasBlockedLeader($leader->id)){
            return response()->json(['message' => 'This rookie has blocked you'], 403);
        }

        $channel = PubnubChannel::query()
            ->where('leader_id', $leader->id)
            ->where('rookie_id', $rookie->id)
            ->exists();

        if(!$channel){
            //Well the structure won't allow differently
            NotificationUtils::sendNotification($leader->id, 'leader_free_subscription', now(), [
                'ref_user_id' => $rookie->id
            ]);
            NotificationUtils::sendNotification($rookie->id, 'rookie_free_subscription', now(), [
                'ref_user_id' => $leader->id
            ]);
            Chat::config($leader->id)->startDirectChat(
                User::find($leader->id),
                User::find($rookie->id),
                null,
                null,
                false,
                false,
                $goal->id
            );
        }

        $validator = Validator::make($request->all(), [
            'amount' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        if($leader->micro_morgi_balance < $request->amount) {
            return response()->json(['message' => trans('leader.low_micromorgi')], 400);
        }

        $goal_donations_amount = GoalDonation::query()
            ->where('goal_id', $goal->id)
            ->where('status', 'successful')
            ->sum('amount');

        $new_goal_amount = $goal_donations_amount + $request->amount;
        if($new_goal_amount > $goal->target_amount){
            $max_amount = $goal->target_amount - $goal_donations_amount;
            return response()->json(['message' => "You can support this goal with up to $max_amount Micro Morgis"], 400);
        }

        DB::beginTransaction();
        try {
            $transaction = TransactionGoal::create($rookie->id, $leader->id, $request->amount, $goal->id);
            SpendMicromorgi::spend($transaction);
            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            return response()->json([
                'message' => 'Error occurred during the transaction',
                'error' => $exception->getMessage()
            ], 400);
        }

        if($new_goal_amount == $goal->target_amount){
            NotificationUtils::sendNotification($goal->rookie_id, 'rookie_goal_amount_reached', now(), [
                'goal_id' => $goal->id
            ]);
            $goal->update([
                'status' => Goal::STATUS_AWAITING_PROOF,
                'end_date' => now()
            ]);
            $broadcast = Broadcast::query()->firstOrCreate([
                'is_goal' => true,
                'sender_id' => $rookie->id,
                'display_name' => $goal->name
            ]);
        }

        $response = GoalResource::compute($request, $goal)->first();
        return response()->json($response);
    }

    public function getPaths(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => ['sometimes', 'integer', 'min:1'],
            'name' => ['sometimes', 'required'],
            'type_id' => ['sometimes', 'required']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $paths = Path::query()
            ->join('users_paths', 'paths.id', '=', 'users_paths.path_id')
            ->join('goals', 'users_paths.user_id', '=', 'goals.rookie_id')
            ->where('goals.status', Goal::STATUS_ACTIVE)
            ->whereNull('goals.deleted_at');


        if($request->has('time_range')){
            $paths
              ->where('goals.end_date', '<', now()->addHours($request->time_range))
              ->where('goals.end_date', '>', now());
        } else {
            $paths->where('goals.end_date', '>', now());
        }

        if ($request->has('name')){
            $search_key = "%$request->name%";
            $paths = $paths->where('paths.name', 'like' , $search_key);
        }

        $paths_count = $paths->clone()
            ->selectRaw('count(distinct goals.id) as goal_by_category, goals.type_id, goals.status')
            ->groupBy('goals.type_id')
            ->get()
            ->pluck('goal_by_category', 'type_id')
            ->toArray();

        if($request->has('type_id')){
            $paths = $paths->where('goals.type_id', $request->type_id);
        }

        $paths->selectRaw('paths.*, count(distinct goals.id) as goal_count')->groupBy('paths.id');
        $paths = $paths->distinct()->get();

        //TODO: fare la resource
        $paths = self::customPaginate($paths, 100, ['type_counter' => $paths_count]);

        return response()->json($paths);
    }

    public function supportedGoals(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_by' => ['sometimes', Rule::in(['target_amount'])],
            'order_direction' => ['sometimes', Rule::in(['asc', 'desc'])],
            'time_range' => ['sometimes', 'integer', 'min:1'],
            'is_active' => ['sometimes', 'boolean']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $supported_goals_ids = GoalDonation::query()->select('goal_id')
            ->where('leader_id', $request->user()->id)
            ->groupBy('goal_id')
            ->pluck('goal_id')
            ->toArray();

        $blocked_ids = UserUtils::getUsersBlockIds($request->user()->id);

        $goals = Goal::query()
            ->select('goals.*')
            ->whereNotIn('goals.rookie_id', $blocked_ids)
            ->whereIn('id', $supported_goals_ids);

        $goals = (isset($request->is_active) && $request->boolean('is_active'))
            ? $goals->whereActive()
            : $goals->whereNotActive();

        if ($request->has('order_by')){
            $goals = $goals->orderBy($request->order_by, strtoupper($request->order_direction));
        }else{
            $goals = $goals->withCount('donations')->orderBy('donations_count', 'DESC');
        }

        if ($request->has('time_range')){
            $goals = $goals->timeRangeInHours($request->time_range);
        }

        $goals = $goals->paginate($request->get('limit', 15));

        $response = GoalResource::compute($request, $goals)->get();
        return response()->json($response);
    }
}
