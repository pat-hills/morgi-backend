<?php

namespace App\Utils\Subscription\Create;

use App\Enums\SubscriptionEnum;
use App\Logger\Logger;
use App\Mixpanel\Events\EventGiftMorgiSuccess;
use App\Mixpanel\Utils\UserProfileUtils;
use App\Models\Leader;
use App\Models\Rookie;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserPath;
use App\Orazio\OrazioHandler;
use App\Services\Chat\Chat;
use App\Telegram\TelegramUtils;
use App\Transactions\Morgi\TransactionRookieMorgiBonus;
use App\Utils\NotificationUtils;
use App\Utils\SubscriptionUtils;
use App\Utils\TransactionUtils;
use App\Utils\Utils;

class SubscriptionCreateUtils
{
    private $type = SubscriptionEnum::TYPE_PAID;

    // Users
    private $rookie_user;
    private $leader_user;
    private $rookie;
    private $leader;

    // Subscription's attribute
    private $next_donation_at;
    private $amount;
    private $photo_id;
    private $leader_payment_method_id;

    private $subscription;
    private $channel;
    private $giveback;

    private $is_leader_first_gift;

    public function __construct(int $leader_id, int $rookie_id, int $amount)
    {
        $rookie_user = User::find($rookie_id);
        $rookie = Rookie::find($rookie_id);
        if(!isset($rookie_user) || !isset($rookie)){
            throw new \Exception("Unable to retrieve rookie");
        }

        $leader_user = User::find($leader_id);
        $leader = Leader::find($leader_id);
        if(!isset($leader_user) || !isset($leader)){
            throw new \Exception("Unable to retrieve leader");
        }

        $this->rookie = $rookie;
        $this->rookie_user = $rookie_user;
        $this->leader = $leader;
        $this->leader_user = $leader_user;
        $this->is_leader_first_gift = !Subscription::where('leader_id', $this->leader->id)->exists();

        /*
         * Validate if leader and rookie are eligible to create new subscription
         */
        try {
            $this->validate();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        $this->computeNextDonationAt();
        $this->computeAmount($amount);
    }

    public static function configure(int $leader_id, int $rookie_id, int $amount): SubscriptionCreateUtils
    {
        try {
            return new SubscriptionCreateUtils($leader_id, $rookie_id, $amount);
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }

    public function setPhotoId(int $photo_id): SubscriptionCreateUtils
    {
        $this->photo_id = $photo_id;
        return $this;
    }

    public function setLeaderPaymentMethodId(int $leader_payment_method_id): SubscriptionCreateUtils
    {
        $this->leader_payment_method_id = $leader_payment_method_id;
        return $this;
    }

    private function computeNextDonationAt(): void
    {
        $this->next_donation_at = SubscriptionUtils::computeNextDonationAt(now()->toDateTimeString(), now()->toDateTimeString());
    }

    private function computeAmount(int $amount): void
    {
        $this->amount = $amount;
        $this->taxed_amount = TransactionUtils::getTaxedAmountDollars($this->amount);
    }

    private function validate(): void
    {
        $subscription_exists = Subscription::search($this->leader->id, $this->rookie->id)->exists();
        if($subscription_exists){
            throw new \Exception("Subscription already exists");
        }
    }

    private function createSubscription(): void
    {
        $attributes = [
            'leader_id' => $this->leader->id,
            'rookie_id' => $this->rookie->id,
            'amount' => $this->amount,
            'next_donation_at' => $this->next_donation_at,
            'valid_until_at' => $this->next_donation_at,
            'type' => $this->type,
            'subscription_at' => now(),
            'last_subscription_at' => now(),
            'status' => 'active'
        ];

        if(isset($this->photo_id)){
            $attributes['photo_id'] = $this->photo_id;
        }

        if(isset($this->leader_payment_method_id)){
            $attributes['leader_payment_method_id'] = $this->leader_payment_method_id;
        }

        $subscription = Subscription::create($attributes);
        $this->subscription = $subscription->refresh();
    }

    private function leaderUnlockRookiePath(): void
    {
        $path = UserPath::query()->where('user_id', $this->rookie->id)
            ->where('is_subpath', false)
            ->first();

        if(isset($path) && !$this->leader->hasPath($path->path_id)){
            $this->leader->unlockPath($path->path_id);
        }
    }

    private function storeActionTracking(): void
    {
        $action_tracking = $this->leader->retrieveOrCreateActionTracking($this->rookie->id);
        $action_tracking->update(['paid_rookie' => true]);
    }

    private function incrementUsersTotalSubscriptionsCount(): void
    {
        $this->rookie_user->increment('total_subscriptions_count');
        $this->leader_user->increment('total_subscriptions_count');
    }

    private function sendGivebackToLeader(): void
    {
        $giveback = $this->leader->getGiveback($this->leader_user->total_subscriptions_count);
        if(isset($giveback)){
            $this->leader->sendGiveback($giveback);
            $this->giveback = $giveback;
        }
    }

    private function giveMorgiBonusToRookieReferredLeader(): void
    {
        if(!isset($this->leader_user->referred_by) || isset($this->leader_user->referral_bonus_transaction_id) || $this->leader_user->referred_by === $this->rookie->id){
            return;
        }

        $referral_rookie = User::find($this->leader_user->referred_by);
        if(isset($referral_rookie) && $referral_rookie->active){
            $referral_transaction = TransactionRookieMorgiBonus::create(
                $referral_rookie->id,
                10,
                'For a Morgi Friend who has gifted Morgis'
            );
            $this->leader_user->update(['referral_bonus_transaction_id' => $referral_transaction->id]);
        }
    }

    private function openDirectChannel(): void
    {
        try {
            $this->channel = Chat::config($this->leader_user->id)->startDirectChat(
                $this->leader_user, $this->rookie_user, $this->subscription->id
            );
        } catch (\Exception $e) {
        }
    }

    private function refreshOrazio(): void
    {
        try {
            OrazioHandler::freshSeen($this->leader->id, 'New subscription', true);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }
    }

    private function sendTelegramBotNotificationToRookie(): void
    {
        if(!isset($this->rookie_user->joined_telegram_bot_at, $this->channel)){
            return;
        }

        $data = [
            'leader_username' => $this->leader_user->username,
            'amount' => $this->amount,
            'channel_name' => $this->channel->name
        ];

        TelegramUtils::sendTelegramNotifications($this->rookie_user->telegram_chat_id, 'first_gift', $data, $this->rookie_user->id);
    }

    private function sendNotifications(): void
    {
        $now = now();

        if($this->is_leader_first_gift){
            NotificationUtils::sendNotification($this->leader->id, "leader_first_gift_to_rookie", $now);
        }else{
            NotificationUtils::sendNotification($this->leader->id, "leader_new_gift", $now,
                ['ref_user_id' => $this->rookie->id, 'amount_morgi' => $this->amount]);

            if(isset($this->giveback)){
                NotificationUtils::sendNotification($this->leader->id, "giveback", $now, [
                    'reason' => $this->leader_user->total_subscriptions_count . Utils::getNumberNumerals($this->leader_user->total_subscriptions_count),
                    'amount_micromorgi' => $this->giveback
                ]);
            }
        }

        $is_rookie_first_gift = !Subscription::where('rookie_id', $this->rookie->id)->exists();
        $rookie_notification_type = ($is_rookie_first_gift)
            ? 'rookie_first_gift_from_leader'
            : 'rookie_new_gift';

        NotificationUtils::sendNotification($this->rookie->id, $rookie_notification_type, $now,
            ['ref_user_id' => $this->leader->id, 'amount_morgi' => $this->amount]);
    }

    public function create(bool $is_one_click_payment = true): Subscription
    {
        try {
            $this->createSubscription();
            $this->leaderUnlockRookiePath();
            $this->storeActionTracking();
            $this->incrementUsersTotalSubscriptionsCount();
            $this->sendGivebackToLeader();
            $this->giveMorgiBonusToRookieReferredLeader();
            $this->openDirectChannel();
            $this->sendTelegramBotNotificationToRookie();
            $this->sendNotifications();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        if($this->is_leader_first_gift){
            $this->rookie->increment('leaders_first_subscription');
        }

        if(!isset($this->channel->time_to_subscribe)){
            $this->channel->update([
                'time_to_subscribe' => now()->timestamp - strtotime($this->channel->created_at)
            ]);
        }

        try {
            UserProfileUtils::storeOrUpdate($this->rookie->id);
            UserProfileUtils::storeOrUpdate($this->leader->id);
            EventGiftMorgiSuccess::config($this->leader->id, $this->rookie->id, $this->amount, $is_one_click_payment);
            $this->refreshOrazio();
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        return $this->subscription;
    }
}
