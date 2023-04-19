<?php

namespace App\Observers;

use App\Models\PaymentPlatformRookie;
use Illuminate\Support\Facades\Auth;

class PaymentPlatformRookieObserver
{
    /**
     * Handle the PaymentPlatformRookie "deleted" event.
     *
     * @param  \App\Models\PaymentPlatformRookie  $paymentPlatformRookie
     * @return void
     */
    public function deleted(PaymentPlatformRookie $paymentPlatformRookie)
    {
        if(!$paymentPlatformRookie->main){
            return;
        }

        $payment_platform = PaymentPlatformRookie::query()
            ->where('rookie_id', $paymentPlatformRookie->rookie_id)
            ->latest()
            ->first();

        if(isset($payment_platform)){
            $payment_platform->update(['main' => true]);
        }
    }
}
