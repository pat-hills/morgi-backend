<?php

namespace App\Utils\Admin;

use App\Enums\UserEnum;
use App\Models\Country;
use App\Models\LeaderCcbillData;
use App\Models\PaymentPlatformRookie;
use App\Models\Photo;
use App\Models\Region;
use App\Models\Rookie;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserLoginHistory;
use Carbon\Carbon;

class UserRelatedAccountUtils {

    public static function getLeaderRelatedAccount(User $user)
    {
        if($user->type !== UserEnum::TYPE_LEADER){
            throw new \Exception("You are not a leader!");
        }

        $countries = Country::all();

        $login_history = UserLoginHistory::query()->where('user_id', $user->id)->get();

        $user->signup_login = $login_history->where('is_signup_values', true)->first();
        $user->latest_login = $login_history->last();
        $user->signup_country = $countries->where('id', $user->signup_country_id)->first();
        $user->count_logins = $login_history->count();

        $user_transactions = Transaction::query()->where('leader_id', $user->id)->get();

        $first_purchase = $user_transactions->where('type', 'bought_micromorgi')->first();

        $user->first_purchase = isset($first_purchase)
            ? $first_purchase->created_at
            : 'none';

        $last_purchase = $user_transactions->where('type', 'bought_micromorgi')->last();
        $user->last_purchase = isset($last_purchase)
            ? $last_purchase->created_at
            : 'none';

        $user->cgb_refund = $user_transactions->where('type', '=', 'refund')->count();

        $user->morgi_tot = $user_transactions->where('type', 'gift')
            ->whereNull('refund_type')
            ->sum('morgi');

        $user->micro_morgi_tot = $user_transactions->where('type', 'bought_micromorgi')
            ->whereNull('refund_type')
            ->sum('micromorgi');

        $user->has_pic = Photo::query()->where('user_id', $user->id)->limit(1)->count();

        $leader_ccbill_data = LeaderCcbillData::query()->where('leader_id', $user->id)
            ->where('active', true)
            ->latest('id')
            ->first();

        $user->billing_country = isset($leader_ccbill_data)
            ? $leader_ccbill_data->billingCountry
            : 'none';

        $user->ucid = isset($leader_ccbill_data)
            ? $leader_ccbill_data->paymentAccount
            : 'none';

        $exploded_email = explode("@", $user->email);
        list($email_name, $domain) = $exploded_email;
        $username_email = $email_name;

        $users_ids = [];

        $matches_ucid = [];
        if ($user->ucid !== 'none') {

            $matches_ucid = LeaderCcbillData::query()
                ->whereNotIn('leader_id', [$user->id])
                ->where('paymentAccount', 'LIKE', "%{$user->ucid}%")
                ->pluck('paymentAccount', 'leader_id')
                ->toArray();

            $users_ids = array_merge($users_ids, array_keys($matches_ucid));
        }

        $matches_email = User::query()
            ->whereNotIn('id', [$user->id])
            ->where('email', 'LIKE', "%{$username_email}%")
            ->pluck('email', 'id')
            ->toArray();

        $users_ids = array_merge($users_ids, array_keys($matches_email));

        $login_history_ip_address = $login_history->pluck('ip_address');
        $matches_ip = UserLoginHistory::query()
            ->where('user_id', '!=', $user->id)
            ->whereIn('ip_address', $login_history_ip_address)
            ->whereDate('created_at', '>', Carbon::now()->subDays(30))
            ->pluck('ip_address', 'user_id')
            ->toArray();

        $users_ids = array_merge($users_ids, array_keys($matches_ip));

        $how_many_matches = array_count_values($users_ids);

        $users_ids = array_unique($users_ids);

        $users = User::query()
            ->where('type', UserEnum::TYPE_LEADER)
            ->whereIn('id', $users_ids)
            ->get();

        $users_leaderCcbillData = LeaderCcbillData::query()->whereIn('leader_id', $users_ids)->get();

        $users_photos = Photo::query()->whereIn('user_id', $users_ids)->get();

        $users_transactions = Transaction::query()->whereIn('leader_id', $users_ids)->get();

        $users_logins = UserLoginHistory::query()->whereIn('user_id', $users_ids)->get();

        $matched_users = [];
        foreach ($users as $matched_user){

            $matched_user->signup_country_name = $countries->where('id', $matched_user->signup_country_id)->first();

            $user_leaderCcbillData = $users_leaderCcbillData->where('leader_id', $matched_user->id)->first();

            $matched_user->ucid = null;
            $matched_user->billing_country = null;

            if (isset($user_leaderCcbillData)) {
                $matched_user->ucid = $user_leaderCcbillData->paymentAccount;
                $matched_user->billing_country = $user_leaderCcbillData->billingCountry;
            }

            $matched_user->is_ucid_match = false;
            if (array_key_exists($matched_user->id, $matches_ucid)) {
                $matched_user->ucid = $matches_ucid[$matched_user->id];
                $matched_user->is_ucid_match = true;
            }

            $matched_user->has_pic = $users_photos->where('user_id', $matched_user->id)->count();

            $user_transactions = $users_transactions->where('leader_id', $matched_user->id);

            $first_purchase = $user_transactions->where('type', 'bought_micromorgi')
                ->first();

            $matched_user->first_purchase = (isset($first_purchase))
                ? $first_purchase->created_at
                : 'none';

            $last_purchase = $user_transactions->where('type', 'bought_micromorgi')
                ->first();

            $matched_user->last_purchase = (isset($last_purchase))
                ? $last_purchase->created_at
                : 'none';

            $matched_user->cgb_refund = $user_transactions->where('type', 'refund')
                ->count();

            $matched_user->count_logins = $users_logins->where('user_id', $matched_user->id)->count();
            $matched_user->found = $how_many_matches[$matched_user->id];

            $matched_user->morgi_tot = $user_transactions->where('type','gift')
                ->whereNull('refund_type')
                ->sum('morgi');

            $matched_user->micro_morgi_tot = $user_transactions->where('type','bought_micromorgi')
                ->whereNull('refund_type')
                ->sum('micromorgi');

            $matched_user->is_email_match = false;
            if (array_key_exists($matched_user->id, $matches_email)) {
                $matched_user->email_match = $matches_email[$matched_user->id];
                $matched_user->is_email_match = true;
            }

            $user_last_login = $users_logins->where('user_id', $matched_user->id)->last();
            $matched_user->ip_address = isset($user_last_login)
                ? $user_last_login->ip_address
                : null;

            $matched_user->is_ip_address_match = false;
            if (array_key_exists($matched_user->id, $matches_ip)) {
                $matched_user->ip_address = $matches_ip[$matched_user->id];
                $matched_user->is_ip_address_match = true;
            }

            $matched_users[] = $matched_user;
        }

        return array(
            'user' => $user,
            'matched_users' => $matched_users
        );
    }


    public static function getRookieRelatedAccount(User $user): array
    {
        if($user->type !== UserEnum::TYPE_ROOKIE){
            throw new \Exception("You are not a rookie!");
        }

        $user = Rookie::query()
            ->select('users.*', 'rookies.last_name', 'rookies.birth_date', 'rookies.country_id', 'rookies.region_id', 'rookies.region_name')
            ->where('users.id', $user->id)
            ->join('users', 'rookies.id', '=', 'users.id')
            ->first();

        $user_login_history = UserLoginHistory::query()
            ->where('user_id', $user->id)
            ->get();

        $user->signup_login = $user_login_history
            ->where('is_signup_values', 1)
            ->first();

        $user->latest_login = $user_login_history
            ->last();

        $user_payment_info = PaymentPlatformRookie::query()
            ->where('rookie_id', $user->id)
            ->get();

        $user_transactions = Transaction::query()
            ->where('rookie_id', $user->id)
            ->get();

        $user->morgi_tot = $user_transactions->where('type', 'gift')
                ->whereNull('refund_type')
                ->sum('morgi');

        $exploded_email = explode("@", $user->email);
        list($email_name, $domain) = $exploded_email;
        $username_email = $email_name;

        $users_ids = [];

        $email_matches = User::query()
            ->whereNotIn('id', [$user->id])
            ->where('type', UserEnum::TYPE_ROOKIE)
            ->where('email', 'LIKE', "%{$username_email}%")
            ->pluck('email', 'id')
            ->toArray();

        $users_ids = array_merge($users_ids, array_keys($email_matches));

        $last_name_matches = Rookie::query()
            ->whereNotIn('id', [$user->id])
            ->where('last_name', 'LIKE', "%{$user->last_name}%")
            ->pluck('last_name', 'id')
            ->toArray();

        $users_ids = array_merge($users_ids, array_keys($last_name_matches));

        $birthdate_matches = Rookie::query()
            ->whereNotIn('id', [$user->id])
            ->whereDate('birth_date', $user->birth_date)
            ->pluck('birth_date', 'id')
            ->toArray();

        $users_ids = array_merge($users_ids, array_keys($birthdate_matches));

        $location_matches = Rookie::query()
            ->whereNotIn('id', [$user->id])
            ->where('country_id', $user->country_id)
            ->where(function ($query) use($user){
                $query->where('region_id', '=', $user->region_id)
                    ->orWhere('region_name', '=', $user->region_name);
            })
            ->get();

        $users_ids = array_merge($users_ids, $location_matches->pluck('id')->toArray());

        $ip_matches = UserLoginHistory::query()
            ->whereNotIn('user_id', [$user->id])
            ->whereIn('ip_address', $user_login_history->pluck('ip_address'))
            ->whereDate('created_at', '>', Carbon::now()->subDays(30))
            ->get()
            ->groupBy('user_id');

        $users_ids = array_merge($users_ids, array_keys($ip_matches->toArray()));

        $users_platforms = collect();
        if($user_payment_info->isNotEmpty()){
            $users_platforms = PaymentPlatformRookie::query()
                ->whereNotIn('rookie_id', [$user->id])
                ->whereIn('payment_info', $user_payment_info->pluck('payment_info'))
                ->pluck('payment_info', 'rookie_id');

            $users_ids = array_merge($users_ids, array_keys($users_platforms->toArray()));
        }

        $users_count_matches = array_count_values($users_ids);

        $users_matched = User::query()
            ->select([
                'rookies.id',
                'rookies.first_name',
                'rookies.last_name',
                'rookies.birth_date',
                'rookies.country_id',
                'rookies.region_name',
                'rookies.region_id',
                'users.status',
                'users.email'
            ])
            ->join('rookies', 'rookies.id', '=', 'users.id')
            ->whereNotIn('users.id', [$user->id])
            ->whereIn('users.id', array_keys($users_count_matches))
            ->get();

        $users_matched_logins = UserLoginHistory::query()
            ->whereIn('user_id', $users_matched->pluck('id'))
            ->get();

        $users_payments_details = PaymentPlatformRookie::query()
            ->whereIn('rookie_id', $users_matched->pluck('id'))
            ->get();

        $users_countries = Country::query()
            ->whereIn('id', $users_matched->pluck('country_id'))
            ->get();

        $users_regions = Region::query()
            ->whereIn('id', $users_matched->pluck('region_id'))
            ->get();

        $matched_users = [];
        foreach ($users_matched as $matched_user) {
            if(!array_key_exists($matched_user->id, $users_count_matches)){
                continue;
            }

            $count_matches = $users_count_matches[$matched_user->id];
            if ($count_matches <= 1) {
                continue;
            }

            $matched_user->found = $count_matches;

            $matched_user->is_email_match = (array_key_exists($matched_user->id, $email_matches));

            $matched_user->is_surname_match = (array_key_exists($matched_user->id, $last_name_matches));

            $matched_user->is_bd_match = array_key_exists($matched_user->id, $birthdate_matches);

            $user_login = $users_matched_logins->where('user_id', $matched_user->id)->last();
            $matched_user->ip_address = isset($user_login)
                ? $user_login->ip_address
                : 'none';
            $matched_user->is_ip_address_match = false;
            $matched_user->counter_ip_matches = 0;
            if (array_key_exists($matched_user->id, $ip_matches->toArray())) {
                $ip_match_info = $ip_matches[$matched_user->id]->first();
                if(isset($ip_match_info)){
                    $matched_user->ip_address = $ip_match_info->ip_address;
                    $matched_user->is_ip_address_match = true;
                    $matched_user->counter_ip_matches = count($ip_matches[$matched_user->id]);
                }
            }

            $matched_user->email_match = null;
            $matched_user->is_email_match = false;
            if (array_key_exists($matched_user->id, $email_matches)) {
                $matched_user->email_match = $email_matches[$matched_user->id];
                $matched_user->is_email_match = true;
            }

            $user_payment_detail = $users_payments_details->where('rookie_id', $matched_user->id)->first();

            $matched_user->payment_details = isset($user_payment_detail)
            ? $user_payment_detail->payment_info
            : null;
            $matched_user->is_payment_match = false;
            if($users_platforms->isNotEmpty() && array_key_exists($matched_user->id, $users_platforms->toArray())){
                $matched_user->payment_details = $users_platforms[$matched_user->id];
                $matched_user->is_payment_match = true;
            }


            $country = $users_countries->where('id', $matched_user->country_id)->first();
            $country_name = null;
            if(isset($country)){
                $country_name = $country->name;
            }

            $region_name = $matched_user->region_name;

            if(isset($matched_user->region_id)){
                $region = $users_regions->where('id', $matched_user->region_id)->first();
                if (isset($region)){
                    $region_name = $region->name;
                }
            }

            $matched_user->location = $country_name;
            if(isset($region_name)){
                $matched_user->location .= ", {$region_name}";
            }

            $location_matched = $location_matches->where('id', $matched_user->id)->first();
            if(isset($location_matched)){
                $matched_user->is_location_match = true;
            }

            $matched_users[] = $matched_user;
        }

        return array(
            'user' => $user,
            'matched_users' => $matched_users
        );
    }
}
