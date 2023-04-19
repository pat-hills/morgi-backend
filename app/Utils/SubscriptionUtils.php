<?php


namespace App\Utils;


use Carbon\Carbon;

class SubscriptionUtils
{
    public static function computeNextDonationAt(string $subscription_at, string $last_subscription_at): string
    {
        $day_to_bill = Carbon::create($subscription_at)->day;
        $next_donation_at = Carbon::create($last_subscription_at)->setDay($day_to_bill)->addMonth();

        $last_billed_month = Carbon::create($last_subscription_at)->month;
        $next_billing_month = ($last_billed_month===12) ? 1 : $last_billed_month + 1;

        if ($next_donation_at->month!==$next_billing_month){
            $next_donation_at = $next_donation_at->subMonth()->lastOfMonth();
        }

        return $next_donation_at->toDateTimeString();
    }
}
