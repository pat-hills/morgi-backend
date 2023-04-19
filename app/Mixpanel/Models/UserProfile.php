<?php

namespace App\Mixpanel\Models;

use App\Models\Country;
use App\Models\Gender;
use App\Models\Photo;
use App\Models\PubnubChannel;
use App\Models\Subscription;
use App\Models\User;

class UserProfile
{
    protected $user;
    public $id;
    public $user_type;
    public $signup_source;
    public $referral_type;
    public $avatar;
    public $email;
    public $username;
    public $description;
    public $has_telegram_bot;
    public $country;
    public $timezone;
    public $gender;
    public $total_paid_connections; // Total Subscriptions count
    public $total_active_paid_connections;  // Active subscriptions count
    public $total_connections; // Total connections opened
    public $active_connections_count; // Non paused connections count
    public $recurring_paid_connections_count; // Count of subscriptions billed 3 times
    public $persona;
    public $total_paused_connections;
    public $total_successful_paused_connections;
    public $advertisement_id;
    public $is_active;
    public $created_at;

    public static function config(int $user_id)
    {
        $class = static::class;
        return new $class($user_id);
    }

    public function __construct(int $user_id)
    {
        $this->user = User::find($user_id);
        $this->id = $this->user->id;
        $this->user_type = $this->user->type;
        $this->signup_source = $this->user->signup_source;
        $this->referral_type = $this->computeReferralType();
        $this->avatar = $this->user->getPublicAvatar()['url'] ?? null;
        $this->email = $this->user->email;
        $this->username = $this->user->username;
        $this->created_at = $this->user->created_at;
        $this->description = $this->user->getOwnDescription();
        $this->persona = $this->user->persona;
        $this->has_telegram_bot = isset($this->user->joined_telegram_bot_at);
        $this->total_paid_connections = $this->user->total_subscriptions_count;
        $this->total_active_paid_connections = $this->computeTotalActivePaidConnections();
        $this->total_connections = $this->computeTotalConnections();
        $this->active_connections_count = $this->computeActiveConnectionsCount();
        $this->recurring_paid_connections_count = $this->computeRecurringPaidConnectionsCount();
        $this->total_paused_connections = $this->user->total_paused_connections;
        $this->total_successful_paused_connections = $this->user->total_successful_paused_connections;
        $this->advertisement_id = $this->user->advertisement_id;
        $this->is_active = $this->user->active;

        $signup_country = Country::find($this->user->signup_country_id);

        $this->country = (isset($signup_country)) ? $signup_country->name : 'None';
        $this->timezone = (isset($signup_country)) ? $signup_country->timezone : 'None';

        $gender = Gender::find($this->user->gender_id);
        $this->gender = (isset($gender)) ? $gender->name : 'None';
    }

    public function toArray(): array
    {
        return [
            '$avatar' => $this->avatar,
            '$email' => $this->email,
            '$distinct_id' => $this->id,
            '$name' => $this->username,
            '$created' => $this->created_at,
            'Type' => $this->user_type,
            'Signup source' => $this->signup_source,
            'Referral type' => $this->referral_type,
            'Description' => $this->description,
            'Has telegram bot?' => $this->has_telegram_bot,
            'Timezone' => $this->timezone,
            'Country' => $this->country,
            'Gender' => $this->gender,
            'Total paid connections' => $this->total_paid_connections,
            'Total active paid connections' => $this->total_active_paid_connections,
            'Total connections' => $this->total_connections,
            'Active connections count' => $this->active_connections_count,
            'Recurring paid connections count' => $this->recurring_paid_connections_count,
            'Persona' => $this->persona,
            'Total paused connections' => $this->total_paused_connections,
            'Total successful paused connections' => $this->total_successful_paused_connections,
            'Is Active' => $this->is_active
        ];
    }

    private function computeReferralType(): string
    {
        if(!isset($this->user->referred_by)){
            return 'None';
        }

        return ($this->user === 'rookie') ? 'Leader Referral' : 'Referral';
    }

    private function computeTotalActivePaidConnections(): int
    {
        $user_type_field = ($this->user_type === 'rookie') ? 'rookie_id' : 'leader_id';
        return Subscription::query()->where($user_type_field, $this->id)
            ->where('status', 'active')
            ->whereNull('deleted_at')
            ->count();
    }

    private function computeTotalConnections(): int
    {
        $user_type_field = ($this->user_type === 'rookie') ? 'rookie_id' : 'leader_id';
        return PubnubChannel::query()->where($user_type_field, $this->id)->count();
    }

    private function computeRecurringPaidConnectionsCount(): int
    {
        $user_type_field = ($this->user_type === 'rookie') ? 'rookie_id' : 'leader_id';
        return Subscription::query()->selectRaw('subscriptions.id')
            ->join('leaders_payments', 'leaders_payments.subscription_id', '=', 'subscriptions.id')
            ->where("subscriptions.$user_type_field", $this->id)
            ->where('subscriptions.status', 'active')
            ->where('leaders_payments.status', 'paid')
            ->whereNull('subscriptions.deleted_at')
            ->havingRaw("COUNT(leaders_payments.id) >= 3")
            ->count();
    }

    private function computeActiveConnectionsCount(): int
    {
        $user_type_field = ($this->user_type === 'rookie') ? 'rookie_id' : 'leader_id';
        return PubnubChannel::query()->where($user_type_field, $this->id)
            ->where('is_paused', false)
            ->where('active', true)
            ->count();
    }
}
