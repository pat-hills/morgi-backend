<?php

namespace App\Utils;

use App\Models\Leader;
use App\Models\Subscription;
use App\Models\User;

class SubscriptionRenewUtils
{
    public const ACTIONS = [
        'update', 'keep', 'delete'
    ];

    public $subscriptions = [];
    public $subscriptions_with_errors = [];

    public $active_subscriptions_ids_to_update = [];
    public $subscriptions_ids_with_errors_to_update = [];
    public $subscriptions_cancel_to_cancel = [];

    public $leader_user;

    public static function config(User $leader_user, array $subscriptions = [], array $subscriptions_with_errors = []): SubscriptionRenewUtils
    {
        return new SubscriptionRenewUtils($leader_user, $subscriptions, $subscriptions_with_errors);
    }

    public function __construct(User $leader_user, array $subscriptions = [], array $subscriptions_with_errors = [])
    {
        $this->subscriptions = $subscriptions;
        $this->subscriptions_with_errors = $subscriptions_with_errors;
        $this->leader_user = $leader_user;
        $this->mapSubscriptionsByActions();
        $this->cancelSubscriptions();
    }

    private function mapSubscriptionsByActions(): void
    {
        if(!empty($this->subscriptions)){
            foreach ($this->subscriptions as $subscription){

                if($subscription['action'] === 'update'){
                    $this->active_subscriptions_ids_to_update[] = $subscription['id'];
                }

                if($subscription['action'] === 'delete'){
                    $this->subscriptions_cancel_to_cancel[] = $subscription['id'];
                }
            }
        }

        if(!empty($this->subscriptions_with_errors)){
            foreach ($this->subscriptions_with_errors as $subscription){

                if($subscription['action'] === 'update'){
                    $this->subscriptions_ids_with_errors_to_update[] = $subscription['id'];
                }

                if($subscription['action'] === 'delete'){
                    $this->subscriptions_cancel_to_cancel[] = $subscription['id'];
                }
            }
        }
    }

    private function cancelSubscriptions(): void
    {
        Subscription::query()->whereIn('id', $this->subscriptions_cancel_to_cancel)
            ->update([
                'status' => 'canceled',
                'canceled_at' => now(),
                'deleted_at' => now(),
                'sent_reply_reminder_email_at' => null
            ]);
    }

    public function applyNewPaymentMethodToActiveSubscriptionsToUpdate(int $payment_method_id): void
    {
        Subscription::query()
            ->whereIn('id', $this->active_subscriptions_ids_to_update)
            ->update([
                'leader_payment_method_id' => $payment_method_id
            ]);
    }

}
