<?php

namespace App\Mixpanel\Models;

use App\Models\Gender;
use App\Models\Impression;
use App\Models\Leader;
use App\Models\LeaderPayment;
use App\Models\Path;
use App\Models\PubnubChannel;
use App\Models\SmsSent;
use App\Models\SpenderGroup;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserBlock;
use App\Models\UserReferralEmailsSent;
use App\Queries\LeaderPackageQueries;

class LeaderProfile extends UserProfile
{
    protected $leader;
    public $gender_interested_in;
    public $leader_main_path;
    public $leader_common_paths;
    public $total_micromorgi_bought;
    public $referred_rookies_count;
    public $referral_invitations_sent_to_rookies_count;
    public $blocked_rookies;
    public $carousel_impressions_count;
    public $carousel_impressions_count_last_week;
    public $spender_group;
    public $spender_group_source;
    public $total_morgi_paid_dollar;
    public $total_micromorgi_paid_dollar;
    public $total_morgi_paid;
    public $total_micromorgi_paid;
    public $total_refunds_dollar; // $ Sum refunded
    public $converters_connected; // How many converter chat the leader has
    public $micromorgi_balance;
    public $total_connections_replied; // Every connection with at least one message from the rookie
    public $total_packages_bought;

    public function __construct(int $user_id)
    {
        parent::__construct($user_id);

        $this->leader = Leader::find($this->id);
        $this->micromorgi_balance = $this->leader->micro_morgi_balance;
        $this->gender_interested_in = $this->computeGenderInterestedIn();
        $this->leader_main_path = $this->computeLeaderMainPath();
        $this->leader_common_paths = $this->computeLeaderCommonPaths();
        $this->total_micromorgi_bought = $this->computeTotalMicromorgiBought();
        $this->referred_rookies_count = $this->computeReferredRookiesCount();
        $this->referral_invitations_sent_to_rookies_count = $this->computeReferralInvitationsSentToRookiesCount();
        $this->blocked_rookies = $this->computeBlockedRookies();
        $this->spender_group = $this->computeSpenderGroup();
        $this->carousel_impressions_count = $this->computeCarouselImpressionsCount();
        $this->carousel_impressions_count_last_week = $this->computeCarouselImpressionsCountLastWeek();
        $this->converters_connected = $this->computeConvertersConnected();
        $this->total_refunds_dollar = $this->computeTotalRefunds();
        $this->total_morgi_paid = $this->computeTotalMorgiPaid();
        $this->total_morgi_paid_dollar = $this->computeTotalMorgiPaid();
        $this->total_micromorgi_paid = $this->computeTotalMicromorgiPaid();
        $this->total_micromorgi_paid_dollar = $this->computeTotalMicromorgiPaidDollar();
        $this->total_connections_replied = $this->computeTotalConnectionsReplied();
        $this->total_packages_bought = LeaderPackageQueries::getBoughtPackagesCount($this->id);
        $this->spender_group_source = ($this->leader->spender_group_forced_by_admin)
            ? 'Admin'
            : 'System';
    }

    public function toArray(): array
    {
        $user = parent::toArray();
        $rookie = [
            'Gender interested in' => $this->gender_interested_in,
            'Leader main path' => $this->leader_main_path,
            'Leader common paths' => $this->leader_common_paths,
            'Total micromorgi bought' => $this->total_micromorgi_bought,
            'Referred rookies count' => $this->referred_rookies_count,
            'Referral invitations sent to rookies count' => $this->referral_invitations_sent_to_rookies_count,
            'Blocked rookies' => $this->blocked_rookies,
            'Spender group' => $this->spender_group,
            'Spender group source' => $this->spender_group_source,
            'Converters connected' => $this->converters_connected,
            'Total refunds in dollar' => $this->total_refunds_dollar,
            'Total morgi paid' => $this->total_morgi_paid,
            'Total morgi paid in dollar' => $this->total_morgi_paid_dollar,
            'Total micromorgi paid' => $this->total_micromorgi_paid,
            'Total micromorgi paid in dollar' => $this->total_micromorgi_paid_dollar,
            'Micromorgi balance' => $this->micromorgi_balance,
            'Total connections replied' => $this->total_connections_replied,
            'Total Packages Bought' => $this->total_packages_bought
        ];

        return array_merge($user, $rookie);
    }

    private function computeGenderInterestedIn(): string
    {
        $gender = Gender::find($this->leader->interested_in_gender_id);
        return (isset($gender)) ? $gender->name : 'None';
    }

    private function computeLeaderMainPath(): string
    {
        $path = Path::query()->selectRaw('paths.*')
            ->join('leaders_paths', 'leaders_paths.path_id', '=', 'paths.id')
            ->where('leaders_paths.leader_id', $this->id)
            ->where('leaders_paths.is_main', true)
            ->first();

        return (isset($path)) ? $path->name : 'None';
    }

    private function computeLeaderCommonPaths(): array
    {
        $paths = Path::query()->selectRaw('paths.*')
            ->join('leaders_paths', 'leaders_paths.path_id', '=', 'paths.id')
            ->where('leaders_paths.leader_id', $this->id)
            ->where('leaders_paths.is_main', false)
            ->get();

        return ($paths->isNotEmpty())
            ? $paths->pluck('name')->toArray()
            : [];
    }

    private function computeTotalMicromorgiBought(): int
    {
        return LeaderPayment::query()->where('leader_id', $this->id)
            ->where('currency_type', 'micro_morgi')
            ->whereIn('status', ['paid', 'refunded'])
            ->sum('amount');
    }

    private function computeReferredRookiesCount(): int
    {
        return User::query()->where('referred_by', $this->id)
            ->where('type', 'rookie')
            ->count();
    }

    private function computeReferralInvitationsSentToRookiesCount(): int
    {
        $sms_count = SmsSent::query()->where('user_id', $this->id)->where('is_sent', true)->count();
        $emails_count = UserReferralEmailsSent::query()->where('user_id', $this->id)->count();

        return $sms_count + $emails_count;
    }

    private function computeBlockedRookies(): int
    {
        return UserBlock::query()->where('from_user_id', $this->id)->count();
    }

    private function computeSpenderGroup(): string
    {
        $spender_group = SpenderGroup::find($this->leader->spender_group_id);
        return (isset($spender_group)) ? $spender_group->name : 'None';
    }

    private function computeCarouselImpressionsCount(): int
    {
        return Impression::query()->where('from_user_id', $this->id)->count();
    }

    private function computeCarouselImpressionsCountLastWeek(): int
    {
        return Impression::query()->where('from_user_id', $this->id)
            ->where('created_at', '>=', now()->subWeek())
            ->count();
    }

    private function computeConvertersConnected(): int
    {
        return PubnubChannel::query()->selectRaw("pubnub_channels.*")
            ->join('rookies', 'rookies.id', '=', 'pubnub_channels.rookie_id')
            ->where('pubnub_channels.leader_id', $this->leader->id)
            ->where('rookies.is_converter', true)
            ->count();
    }

    private function computeTotalRefunds(): float
    {
        return LeaderPayment::query()->where('leader_id', $this->id)
            ->where('status', 'refunded')
            ->sum('dollar_amount');
    }

    private function computeTotalMorgiPaid(): float
    {
        return LeaderPayment::query()->where('leader_id', $this->id)
            ->whereIn('status', ['refunded', 'paid'])
            ->where('currency_type', 'morgi')
            ->sum('amount');
    }

    private function computeTotalMicromorgiPaid(): float
    {
        return Transaction::query()->where('leader_id', $this->id)
            ->where('type', 'chat')
            ->sum('micromorgi');
    }

    private function computeTotalMicromorgiPaidDollar(): float
    {
        return Transaction::query()->where('leader_id', $this->id)
            ->where('type', 'chat')
            ->sum('dollars');
    }

    private function computeTotalConnectionsReplied(): int
    {
        return PubnubChannel::query()->selectRaw('pubnub_channels.*')
            ->join('pubnub_messages', 'pubnub_messages.channel_id', '=', 'pubnub_channels.id')
            ->where('pubnub_channels.leader_id', $this->id)
            ->where('pubnub_messages.sender_id','pubnub_channels.rookie_id')
            ->count();
    }
}
