<?php

namespace App\Http\Resources;

use App\Http\Resources\Parents\Resource;
use App\Models\Goal;
use App\Models\NotificationType;
use App\Models\User;
use Carbon\Carbon;

class NotificationResource extends Resource
{
    public function small(): NotificationResource
    {
        $this->addAttributes([
            'id',
            'ref_user_id',
            'goal_id',
            'ref_username',
            'new_birth_date',
            'new_username',
            'seen_at',
            'created_at',
            'amount_micromorgi',
            'amount',
            'currency',
            'old_amount',
            'amount_morgi',
            'reason',
            'goal_name',
            'goal_end_date'
        ]);

        $this->addNotificationTypeToResources();
        //$this->addRefUserToResources();
        //$this->addUserToResources();
        $this->addGoalToResources();

        return $this;
    }

    public function regular(): NotificationResource
    {
        $this->small();
        return $this;
    }

    public function extended(): NotificationResource
    {
        $this->regular();

        return $this;
    }

    private function addGoalToResources()
    {
        $goals_ids = $this->resources->pluck('goal_id');
        $goals = Goal::query()->whereIn('id', $goals_ids)->get();
        $goals_resources = GoalResource::compute($this->request, $goals, 'small')->get();

        foreach ($this->resources as $resource) {
            $goal_resource = $goals_resources->where('id', $resource->goal_id)->first();
            $resource->goal = $goal_resource ?? null;
        }

        $this->attributes[] = 'goal';
    }

    private function addUserToResources()
    {
        $users_ids = $this->resources->pluck('user_id');
        $users = User::query()->whereIn('id', $users_ids)->get();
        $users_resources =  UserResource::compute($this->request, $users, 'small')->get();

        foreach ($this->resources as $resource) {
            $user_resource = $users_resources->where('id', $resource->user_id)->first();
            $resource->user = $user_resource;
        }

        $this->attributes[] = 'user';
    }

    private function addRefUserToResources()
    {
        $users_ids = $this->resources->pluck('ref_user_id');
        $users = User::query()->whereIn('id', $users_ids)->get();
        $users_resources =  UserResource::compute($this->request, $users, 'small')->get();

        foreach ($this->resources as $resource) {
            $user_resource = $users_resources->where('id', $resource->ref_user_id)->first();
            $resource->ref_user = $user_resource;
        }

        $this->attributes[] = 'ref_user';
    }

    public function addNotificationTypeToResources()
    {
        $notifications_types_ids = $this->resources->pluck('notification_type_id');
        $notifications_types = NotificationType::query()->whereIn('id', $notifications_types_ids)->get();

        foreach ($this->resources as $resource) {
            $notification_type = $notifications_types->where('id', $resource->notification_type_id)->first();
            $content = (isset($notification_type))
                ? $resource->mapDescription($notification_type)
                : null;

            $resource->notification_type = $notification_type;
            $resource->notification_type->content = $content;
            $resource->content = $content;
            $resource->type = $notification_type->type ?? null;
            $resource->title = $notification_type->title ?? null;
        }

        $this->attributes[] = 'notification_type';
        $this->attributes[] = 'content';
        $this->attributes[] = 'type';
        $this->attributes[] = 'title';
    }

}
