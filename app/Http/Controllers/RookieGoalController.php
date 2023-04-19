<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Broadcast;
use App\Models\BroadcastMessage;
use App\Utils\BroadcastUtils;
use getID3;
use Carbon\Carbon;
use App\Models\Goal;
use App\Models\User;
use App\Models\Rookie;
use App\Models\GoalType;
use App\Models\GoalMedia;
use App\Models\GoalProof;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use App\Models\GoalDonation;
use Illuminate\Http\Request;
use App\Utils\Goal\GoalUtils;
use App\Utils\Upload\UploadUtils;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\GoalResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\SubmitProofRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\GoalValidationRequest;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class RookieGoalController extends Controller
{
    public function index(Request $request, Rookie $rookie)
    {
        $goals = $rookie->goals()->whereActive()->get();
        $goals = GoalResource::compute($request, $goals)->get();

        return response()->json($goals);
    }

    public function store(GoalValidationRequest $request)
    {
        if(!Auth::user()->active){
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $already_has_an_active_goal = Goal::query()
            ->where('rookie_id', Auth::id())
            ->whereIn('status', [
                Goal::STATUS_PENDING,
                Goal::STATUS_ACTIVE,
                Goal::STATUS_AWAITING_PROOF,
                Goal::STATUS_PROOF_PENDING_APPROVAL,
                Goal::STATUS_REVIEW,
                Goal::STATUS_SUSPENDED
            ])->exists();

        if($already_has_an_active_goal){
            return response()->json(['message' => "You can only have one active goal"], 400);
        }

        $goal_type = GoalType::find($request->type_id);

        try {
            $start_date = empty($request->start_date) ? Carbon::now() : new Carbon($request->start_date);
            $end_date = new Carbon($request->end_date);
            GoalUtils::validateGoalDate($start_date, $end_date, $goal_type);
        } catch (BadRequestException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (\Exception $e){
            return response()->json(['message' => "Dates format not valid"], 400);
        }

        if ($request->target_amount < $goal_type->min || $request->target_amount > $goal_type->max) {
            return response()->json(['message' => "Amount not in range of goal type allowed amount"], 400);
        }

        $goal_attributes = array_merge(
            $request->only([
                'name',
                'target_amount',
                'thank_you_message',
                'proof_note',
                'type_id',
                'has_image_proof',
                'has_video_proof',
                'details'
            ]),
            [
                'slug' => Str::slug($request->name . '-' . Str::random(8)),
                'rookie_id'=> Auth::id(),
                'end_date' => $end_date,
                'start_date' => $start_date,
                'status' => Goal::STATUS_PENDING
            ]
        );

        DB::beginTransaction();
        try {
            $goal = Goal::create($goal_attributes);
            if($request->has('path_location')){
                GoalMedia::create([
                    'goal_id' => $goal->id,
                    'type' => GoalMedia::TYPE_IMAGE,
                    'path_location' => $request->path_location
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => trans('rookie.could_not_create_goal'), "error" => $e->getMessage()], 400);
        }

        $goal = GoalResource::compute($request, $goal->refresh())->first();
        return response()->json($goal, 201);
    }

    public function show(Rookie $rookie, Goal $goal, Request $request)
    {
        if ($goal->rookie_id !== $rookie->id){
            return response()->json(["message" => "Goal not found"], 404);
        }

        $response = GoalResource::compute($request, $goal->refresh())->first();
        return response()->json($response);
    }

    public function publicShow(Goal $goal, Request $request)
    {
        if(in_array($goal->status, [Goal::STATUS_CANCELLED, Goal::STATUS_SUSPENDED, Goal::STATUS_PENDING], true)){
            return response()->json([], 404);
        }

        $response = GoalResource::compute($request, $goal)->first();
        return response()->json($response);
    }

    public function update(Request $request, Goal $goal)
    {
        if ($goal->rookie_id !== $request->user()->id){
            return response()->json(["message" => "Goal not found"], 404);
        }

        if($goal->status !== Goal::STATUS_SUSPENDED){
            $validator = Validator::make($request->all(), [
                'end_date' => ['required', 'date']
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                "name" => "string|max:50",
                "details" => "string|max:150",
                "end_date" => "date|required",
                "path_location" => "ends_with:jpeg,jpg,png",
            ]);
        }


        if ($validator->fails()) {
            return $validator->errors();
        }

        try {
            $start_date = new Carbon($goal->start_date);
            $end_date = new Carbon($request->end_date);
            GoalUtils::validateGoalDate($start_date, $end_date, $goal->type);
        } catch (BadRequestException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (\Exception $e){
            return response()->json(['message' => "Dates format not valid"], 400);
        }

        if($goal->status !== Goal::STATUS_SUSPENDED){
            $goal->update([
                'end_date' => $end_date->format('Y-m-d')
            ]);
        } else {
            $request->end_date = $end_date->format('Y-m-d');
            $goal->update([
                $request->only(["name", "details", "end_date", "path_location"])
            ]);
        }

        $response = GoalResource::compute($request, $goal->refresh())->first();
        return response()->json($response);
    }

    public function requestReview(Request $request, Goal $goal)
    {
        if ($goal->rookie_id !== $request->user()->id){
            return response()->json(["message" => "Goal not found"], 404);
        }

        if($goal->status !== Goal::STATUS_SUSPENDED){
            return response()->json(["message" => "Goal not found"], 404);
        }

        $goal->update([
            'status' => Goal::STATUS_REVIEW
        ]);


        $response = GoalResource::compute($request, $goal->refresh())->first();
        return response()->json($response);
    }

    public function delete(Request $request, Goal $goal)
    {
        if ($goal->rookie_id !== $request->user()->id){
            return response()->json(["message" => "Goal not found"], 404);
        }

        GoalUtils::refundGoalDonations($goal->id);
        $goal->delete();

        return response()->json([], 204);
    }

    public function submitProof(SubmitProofRequest $request, Goal $goal)
    {
        /*
         * Check right owner
         */
        if (Auth::id() !== $goal->rookie_id) {
            return response()->json(['message' => trans('rookie.you_are_not_allowed')], 403);
        }

        $proofs = $request->get('proofs');
        $proof_ids = Collection::make($proofs)->pluck('id')->toArray();
        $available_proof_ids = $goal->proofs()
            ->where('status', '!=', GoalProof::STATUS_DECLINED)
            ->get()
            ->pluck('id')
            ->toArray();
        $ids_to_delete = array_diff($available_proof_ids, $proof_ids);
        GoalProof::destroy($ids_to_delete);
        GoalUtils::validateGoalProof($goal,$proofs);

        DB::beginTransaction();
        try {
            foreach ($proofs as $proof) {
                if(!empty($proof['id']) && in_array($proof['id'], $available_proof_ids)){
                    continue;
                }
                GoalProof::create([
                    'goal_id' => $goal->id,
                    'type' => $proof['type'],
                    'path_location' => $proof['url']
                ]);
            }

            $goal->update([
                'proof_note' => $request->message,
                'status' => Goal::STATUS_PROOF_PENDING_APPROVAL
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => trans('rookie.could-not-submit-proof'), "error" => $e->getMessage()], 400);
        }

        //TODO: send proof to leaders here as broadcast
        $response = GoalResource::compute($request, $goal->refresh())->first();

        return response()->json($response, 201);
    }

    public function removeProof(Request $request, Goal $goal, GoalProof $proof)
    {
        /*
         * Check right owner
         */
        if (Auth::id() !== $goal->rookie_id) {
            return response()->json(['message' => trans('rookie.you_are_not_allowed')], 403);
        }

        if($proof->goal_id !== $goal->id){
            return response()->json(['message' => trans('rookie.you_are_not_allowed')], 403);
        }

        $proof->delete();
        return response()->json([], 204);
    }

    public function supporters(Request $request, Goal $goal)
    {
        $supporters = GoalDonation::query()->selectRaw('goal_donations.leader_id')
            ->join('users','goal_donations.leader_id', '=', 'users.id')
            ->where('goal_id', $goal->id)
            ->where('goal_donations.status', 'successful')
            ->groupBy('goal_donations.leader_id')
            ->get();

        $donations_amounts = GoalDonation::query()->selectRaw('leader_id, amount')
            ->whereIn('leader_id', $supporters->pluck('leader_id')->toArray())
            ->where('goal_id', $goal->id)
            ->where('status', 'successful')
            ->get();

        $users = User::query()->whereIn('id', $supporters->pluck('leader_id')->toArray())->get();

        $resources = UserResource::compute(
            $request,
            $users
        )->get();

        foreach ($resources as $resource){
            $leader_donations_amount = $donations_amounts->where('leader_id', $resource->id)->sum('amount');
            $resource->type_attributes->leader_donations_amount = $leader_donations_amount;
        }

        return response()->json($resources);
    }

    public function indexPastGoals(Request $request, Rookie $rookie)
    {
        $goals = $rookie->goals()
            ->where(function($query) {
                $query->whereIn('status', [Goal::STATUS_SUCCESSFUL, Goal::STATUS_AWAITING_PROOF, Goal::STATUS_PROOF_PENDING_APPROVAL])
                    ->orWhere('end_date', '<=', now());
            })
            ->paginate(
                $request->get('limit', 15)
            );

        $goals = GoalResource::compute($request, $goals)->get();

        return response()->json($goals);
    }

    public function cancel(Request $request, Goal $goal)
    {
        if ($goal->rookie_id !== $request->user()->id){
            return response()->json(['message' => trans('rookie.you_are_not_allowed')], 403);
        }

        $goal->update([
            'cancelled_reason' => Goal::CANCEL_REASON_USER_CANCELLED,
            'status' => Goal::STATUS_CANCELLED,
        ]);

        return response()->json($goal);
    }

    public function withdraw(Request $request, Goal $goal)
    {
        if ($goal->rookie_id !== $request->user()->id){
            return response()->json(['message' => trans('rookie.you_are_not_allowed')], 403);
        }

        if ($goal->status === Goal::STATUS_SUSPENDED){
            return response()->json(['message' => 'This goal is currently suspended, you cannot fetch your micromorgis'], 400);
        }

        $donation_sum = $goal->donations()->where('status', 'successful')->sum('amount');
        $goal_donations_percentage = ($donation_sum / $goal->target_amount) * 100;
        if ($goal_donations_percentage < Goal::MINIMUM_SUCCESS_PERCENTAGE) {
            return response()->json(["message" => "This goal didn't reach enough donations for you to withdraw them"], 400);
        }

        Broadcast::query()->firstOrCreate([
            'is_goal' => true,
            'sender_id' => $goal->rookie->id,
            'display_name' => $goal->name
        ]);

        $goal->update(['status' => Goal::STATUS_AWAITING_PROOF]);
        $response = GoalResource::compute($request, $goal)->first();

        return response()->json($response);
    }
}
