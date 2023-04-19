<?php

namespace App\Http\Resources;

use App\Enums\UserEnum;
use App\Http\Resources\Parents\Resource;
use App\Models\Goal;
use App\Models\GoalDonation;
use App\Models\GoalMedia;
use App\Models\GoalProof;
use App\Models\GoalType;
use App\Models\SavedGoal;
use App\Models\User;

class GoalResource extends Resource
{
    public function small(): GoalResource
    {
        $this->addAttributes([
            'id', 'name', 'slug', 'details', 'target_amount',
            'currency_type', 'rookie_id', 'start_date', 'end_date', 'thank_you_message',
            'cancelled_at', 'cancelled_reason', 'type_id', 'status', 'proof_note',
            'created_at', 'has_image_proof', 'has_video_proof', 'broadcast'
        ]);

        if($this->requesting_user){
            $this->addLeaderDonationsAmountToResource();
            $this->addIsSavedToResource();

            if ($this->requesting_user->type === UserEnum::TYPE_ADMIN){
                $this->addCancelledByToResource();
            }
        }

        $this->addMediaToResource();
        $this->addProofsToResource();
        $this->addTypeToResource();
        $this->addDonationsCountToResource();
        $this->addRookieToResource();

        return $this;
    }

    private function addMediaToResource(): void
    {
        $goals_ids = $this->resources->pluck('id')->toArray();
        $goal_media = GoalMedia::query()->whereIn('goal_id', $goals_ids)->get();
        foreach ($this->resources as $resource){
            $media = $goal_media->where('goal_id', $resource->id)->values();
            $resource->media = ($media->isNotEmpty())
                ? $media
                : null;
        }
        $this->attributes[] = 'media';
    }

    private function addProofsToResource(): void
    {
        $goals_ids = $this->resources->pluck('id')->toArray();
        $goal_proofs = GoalProof::query()->whereIn('goal_id', $goals_ids)->get();
        foreach ($this->resources as $resource){
            $proofs = $goal_proofs->where('goal_id', $resource->id)->values();
            $resource->proofs = ($proofs->isNotEmpty())
                ? $proofs
                : null;
        }
        $this->attributes[] = 'proofs';
    }

    private function addTypeToResource(): void
    {
        $goal_types = GoalType::all();
        foreach ($this->resources as $resource){
            $resource->type = $goal_types->where('id', $resource->type_id)->first();
        }
        $this->attributes[] = 'type';
    }

    private function addDonationsCountToResource(): void
    {
        $goals_donations = GoalDonation::query()
            ->whereIn('goal_id', $this->resources->pluck('id'))
            ->get();

        foreach ($this->resources as $resource){

            $goal_donations = $goals_donations->where('goal_id', $resource->id);

            /*
             * On those statuses we must show all transactions.
             * edge case to check with ron: goals that received more than goal's amount (due refunds)
             */
            if(in_array($resource->status, [Goal::STATUS_SUSPENDED, Goal::STATUS_CANCELLED], true)){
                $resource->donations_count = $goal_donations->sum('amount');
                continue;
            }

            $resource->donations_count = $goal_donations->where('status', 'successful')->sum('amount');
        }

        $this->attributes[] = 'donations_count';
    }

    private function addIsSavedToResource(): void
    {
        $saved_goals = SavedGoal::query()
            ->where('leader_id', $this->requesting_user->id)
            ->whereIn('goal_id', $this->resources->pluck('id'))
            ->get();
        foreach ($this->resources as $resource){
            $saved_goal = $saved_goals->where('goal_id', $resource->id)->first();
            $resource->is_saved = isset($saved_goal);
        }

        $this->attributes[] = 'is_saved';
    }

    private function addLeaderDonationsAmountToResource(): void
    {
        $goal_donations = GoalDonation::query()
            ->where('leader_id', $this->requesting_user->id)
            ->whereIn('goal_id', $this->resources->pluck('id'))
            ->where('status', 'successful')
            ->get();

        foreach ($this->resources as $resource){
            $resource->leader_donations_amount = $goal_donations->where('goal_id', $resource->id)->sum('amount');
        }

        $this->attributes[] = 'leader_donations_amount';
    }

    //TODO: fix user with right resource
    private function addRookieToResource(): void
    {
        $rookies_ids = $this->resources->pluck('rookie_id');
        $rookies = User::query()->findMany($rookies_ids);

        foreach ($this->resources as $resource) {

            $rookie = $rookies->where('id', $resource->rookie_id)->first();
            $rookie_response = [
                'id' => $rookie->id,
                'avatar' => $rookie->getPublicAvatar(),
                'gender' => $rookie->getGender(),
                'username' => $rookie->username,
                'full_name' => $rookie->full_name,
            ];

            $resource->rookie = $rookie_response;
        }

        $this->attributes[] = 'rookie';
    }

    private function addCancelledByToResource(): void
    {
        $cancelled_by_ids = $this->resources->whereNotIn('cancelled_by_user_id', $this->resources->pluck('rookie_id'))
            ->pluck('cancelled_by_user_id');

        $users = User::query()
            ->whereIn('id', $cancelled_by_ids)
            ->get();

        foreach ($this->resources as $resource){

            $resource->cancelled_by = null;

            if(!isset($resource->cancelled_by_user_id)){
                continue;
            }

            if(in_array($resource->cancelled_reason, [Goal::CANCEL_REASON_USER_CANCELLED])){
                continue;
            }

            if($resource->cancelled_by_user_id === $resource->rookie_id){
                $resource->cancelled_by = $resource->rookie;
                continue;
            }

            $resource->cancelled_by = $users->where('id', $resource->cancelled_by_user_id)->first();
        }

        $this->attributes[] = 'cancelled_by';
    }
}
