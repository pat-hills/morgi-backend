<?php

namespace App\Http\Resources;

use App\Http\Resources\Parents\Resource;
use App\Models\Goal;
use App\Models\Transaction;
use App\Models\User;

class TransactionResource extends Resource
{
    public function small(): TransactionResource
    {
        $this->attributes = [
            'created_at',
            'description',
            'id',
            'notes',
            'type',
            'refund_type',
            'coupon_id',
            'goal_id',
            'referal_internal_id'
        ];

        $this->addAmountDollarsToResource();
        $this->addAmountMicromorgiToResource();
        $this->addAmountMorgiToResource();
        $this->addExternalIdToResource();
        $this->addReferalExternalIdToResource();
        $this->addIsRefundedToResource();
        $this->addRefundDateToResource();
        $this->addSignToResource();
        $this->addRefundByBillerToResource();
        $this->addIsBonusToResource();
        $this->addRookieToResources();
        $this->addLeaderToResources();
        $this->addGoalToResources();

        return $this;
    }

    public function regular(): TransactionResource
    {
        $this->small();
        return $this;
    }

    public function extended(): TransactionResource
    {
        $this->regular();
        return $this;
    }

    public function addIsBonusToResource()
    {
        $refunded_transactions = Transaction::query()
            ->whereIn('internal_id', $this->resources->pluck('referal_internal_id'))
            ->get();

        // A transaction has is_bonus to true when it is a bonus transaction or its referal transaction is bonus transaction
        foreach ($this->resources as $resource) {

            if ($resource->type === 'refund'){
                $refunded_transaction = $refunded_transactions->where('internal_id', $resource->referal_internal_id)->first();
                $resource->is_bonus = isset($refunded_transaction) && $refunded_transaction->type === 'bonus';
                continue;
            }

            $resource->is_bonus = $resource->type === 'bonus';
        }

        $this->attributes[] = 'is_bonus';
    }

    public function addAmountDollarsToResource()
    {
        foreach ($this->resources as $resource) {
            $resource->amount_dollars = abs($resource->dollars);
        }

        $this->attributes[] = 'amount_dollars';
    }

    public function addRefundByBillerToResource()
    {
        foreach ($this->resources as $resource) {
            $resource->refund_by_biller = $resource->type === 'refund' && !isset($transaction->refunded_by);
        }

        $this->attributes[] = 'refund_by_biller';
    }

    public function addAmountMicromorgiToResource()
    {
        foreach ($this->resources as $resource) {
            $resource->amount_micromorgi = abs($resource->micromorgi);
        }

        $this->attributes[] = 'amount_micromorgi';
    }

    public function addAmountMorgiToResource()
    {
        foreach ($this->resources as $resource) {
            $resource->amount_morgi = abs($resource->morgi);
        }

        $this->attributes[] = 'amount_morgi';
    }

    public function addExternalIdToResource()
    {
        foreach ($this->resources as $resource) {
            $resource->external_id = $resource->internal_id;
        }

        $this->attributes[] = 'external_id';
    }

    public function addReferalExternalIdToResource()
    {
        foreach ($this->resources as $resource) {
            $resource->referal_external_id = $resource->referal_internal_id;
        }

        $this->attributes[] = 'referal_external_id';
    }

    public function addIsRefundedToResource()
    {
        foreach ($this->resources as $resource) {
            $resource->is_refunded = $resource->type === 'refund';
        }

        $this->attributes[] = 'is_refunded';
    }

    public function addRefundDateToResource()
    {
        foreach ($this->resources as $resource) {
            $resource->refund_date = $resource->refunded_at;
        }

        $this->attributes[] = 'refund_date';
    }

    public function addSignToResource()
    {
        $user = $this->request->user();
        if(!isset($user)){
            return;
        }

        foreach ($this->resources as $resource) {
            $resource->sign = $resource->getSignByUserType($user->type);
        }

        $this->attributes[] = 'sign';
    }

    private function addLeaderToResources()
    {
        $leaders_ids = $this->resources->whereNotNull('leader_id')->pluck('leader_id');
        $leaders = User::query()->findMany($leaders_ids);

        foreach ($this->resources as $resource) {

            $leader = $leaders->where('id', $resource->leader_id)->first();

            $resource->leader = (isset($leader)) ? [
                'id' => $leader->id,
                'avatar' => $leader->getPublicAvatar(),
                'description' => $leader->description,
                'username' => $leader->username
            ] : null;
        }

        $this->attributes[] = 'leader';
    }


    private function addRookieToResources()
    {
        $rookies_ids = $this->resources->whereNotNull('rookie_id')->pluck('rookie_id');
        $rookies = User::query()->findMany($rookies_ids);

        foreach ($this->resources as $resource) {

            $rookie = $rookies->where('id', $resource->rookie_id)->first();

            $resource->rookie = (isset($rookie)) ? [
                'id' => $rookie->id,
                'avatar' => $rookie->getPublicAvatar(),
                'username' => $rookie->username,
                'full_name' => $rookie->full_name,
             ] : null;
        }

        $this->attributes[] = 'rookie';
    }

    private function addGoalToResources()
    {
        $goals_ids = $this->resources->whereNotNull('goal_id')->pluck('goal_id');
        $goals = GoalResource::compute(
            $this->request,
            Goal::query()->findMany($goals_ids),
            'small'
        )->get();

        foreach ($this->resources as $resource) {
            $resource->goal = $goals->where('id', $resource->goal_id)->first();
        }

        $this->attributes[] = 'goal';
    }
}
