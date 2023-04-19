<?php

namespace App\Providers;

use App\Models\PaymentPlatformRookie;
use App\Models\Photo;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserDescriptionHistory;
use App\Models\VideoHistory;
use App\Observers\PaymentPlatformRookieObserver;
use App\Observers\PhotoObserver;
use App\Observers\SubscriptionObserver;
use App\Observers\TransactionObserver;
use App\Observers\UserDescriptionHistoryObserver;
use App\Observers\UserObserver;
use App\Observers\VideoHistoryObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Photo::observe(PhotoObserver::class);
        Subscription::observe(SubscriptionObserver::class);
        User::observe(UserObserver::class);
        UserDescriptionHistory::observe(UserDescriptionHistoryObserver::class);
        VideoHistory::observe(VideoHistoryObserver::class);
        PaymentPlatformRookie::observe(PaymentPlatformRookieObserver::class);
        Transaction::observe(TransactionObserver::class);
    }
}
