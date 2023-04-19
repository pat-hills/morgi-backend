<?php

namespace App\Http;


use App\Http\Middleware\EnsureAdminIsLogged;
use App\Http\Middleware\EnsureIsActive;
use App\Http\Middleware\EnsureIsCcbill;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsLeader;
use App\Http\Middleware\EnsureUserIsOperator;
use App\Http\Middleware\EnsureUserIsRookie;

use App\Http\Middleware\EnsureUserSentId;
use App\Http\Middleware\GlobalValidation;
use App\Http\Middleware\IsSendgrid;
use App\Http\Middleware\LastActivity;
use App\Http\Middleware\LocalizationMiddleware;
use App\Http\Middleware\ParseResponseType;
use App\Http\Middleware\SyncPubnub;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Fruitcake\Cors\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        GlobalValidation::class,
        ParseResponseType::class
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:600,1',
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */

    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'operator' => EnsureUserIsOperator::class,
        'admin' => EnsureUserIsAdmin::class,
        'admin_logged' => EnsureAdminIsLogged::class,
        'localization' => LocalizationMiddleware::class,

        'rookie' => EnsureUserIsRookie::class, //TODO delete after switch to v2 finished
        'isRookie' => EnsureUserIsRookie::class, //TODO delete after switch to v2 finished

        'leader' => EnsureUserIsLeader::class, //TODO delete after switch to v2 finished
        'isLeader' => EnsureUserIsLeader::class, //TODO delete after switch to v2 finished

        'ccbill' => EnsureIsCcbill::class, //TODO delete after switch to v2 finished
        'isCcbill' => EnsureIsCcbill::class, //TODO delete after switch to v2 finished

        'untrusted' => EnsureUserSentId::class, //TODO delete after switch to v2 finished
        'isUntrusted' => EnsureUserSentId::class, //TODO delete after switch to v2 finished

        'last_activity' => LastActivity::class, //TODO delete after switch to v2 finished
        'updateLastActivityAt' => LastActivity::class, //TODO delete after switch to v2 finished

        'active' => EnsureIsActive::class, //TODO delete after switch to v2 finished
        'isActive' => EnsureIsActive::class, //TODO delete after switch to v2 finished

        'sendgrid' => IsSendgrid::class, //TODO delete after switch to v2 finished
        'isSendgrid' => IsSendgrid::class, //TODO delete after switch to v2 finished
    ];
}
