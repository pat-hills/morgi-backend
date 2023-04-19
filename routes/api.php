<?php

use App\Http\Controllers\AppInfoController;
use App\Http\Controllers\ChannelReadTimetokenController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\MixpanelController;
use App\Http\Controllers\PubnubMessagesController;
use Illuminate\Support\Facades\Route;
use App\Webhooks\Ccbill\CCbillWebhook;
use App\Http\Controllers\SocialiteAuth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PubnubChannelController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\PathController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\CcbillController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\GenderController;
use App\Http\Controllers\LeaderController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\RookieController;
use App\Webhooks\Sendgrid\SendgridWebhook;
use App\Webhooks\Telegram\TelegramWebhook;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\GivebackController;
use App\Http\Controllers\UserBlockController;
use App\Http\Controllers\MicroMorgiController;
use App\Http\Controllers\RookieGoalController;
use App\Http\Controllers\RookieSavedController;
use App\Http\Controllers\RookieScoreController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\GoalTypeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserReferralController;
use App\Http\Controllers\ComplaintTypeController;
use App\Http\Controllers\BroadcastController;
use App\Http\Controllers\ContentEditorController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ActionTrackingController;
use App\Http\Controllers\ChatAttachmentController;
use App\Http\Controllers\IdentityVerifyController;
use App\Http\Controllers\RookieCarouselController;
use App\Http\Controllers\LeaderQuotationController;
use App\Http\Controllers\BalanceTransactionController;
use App\Http\Controllers\LeaderGoalController;
use App\Http\Controllers\RookieWinnerHistoryController;
use App\Http\Controllers\SavedGoalController;
use App\Webhooks\ElasticTranscoder\ElasticTranscoderWebhook;
use App\Http\Controllers\RookiesConverterRequestController;

Route::get('version', [AppInfoController::class, 'version']);

Route::group(['prefix' => 'auth'], function () {

    Route::get('signup/activate/{token}', [AuthController::class, 'signupActivate']);

    Route::group(['prefix' => 'signup'], function () {
        Route::post('/', [UserController::class, 'signup']); //TODO Move this in AuthController
        Route::post('/facebook', [SocialiteAuth::class, 'facebookSignup']);
        Route::post('/google', [SocialiteAuth::class, 'googleSignup']);
    });

    Route::group(['prefix' => 'attach/{user}'], function () {
        Route::post('/facebook', [SocialiteAuth::class, 'facebookUserAttach']);
        Route::post('/google', [SocialiteAuth::class, 'googleUserAttach']);
    });

    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::group(['middleware' => ['auth:api', 'last_activity']], function () {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'user']);
    });
});

Route::group(['middleware' => ['auth:api', 'last_activity']], function () {
    Route::post('profile/delete', [UserController::class, 'delete']);
});

Route::get('test', [CcbillController::class, 'test']);

Route::group(['prefix' => 'password_recovery'], function () {
    Route::post('create', [PasswordResetController::class, 'create']);
    Route::get('find/{token}', [PasswordResetController::class, 'find']);
    Route::post('reset', [PasswordResetController::class, 'reset'])->name('password.reset');
});

//AUTOCOMPLETE
Route::get('genders', [GenderController::class, 'index']);
Route::get('countries', [CountryController::class, 'index']);
Route::get('cities', [CityController::class, 'index']);
Route::get('regions', [RegionController::class, 'index']);
Route::get('paths', [PathController::class, 'index']);
Route::get('subpaths', [PathController::class, 'indexSubpath']);

Route::get('localize', [CountryController::class, 'localize']);

//Upload of Photo and Video
Route::post('photo', [PhotoController::class, 'store']);
Route::post('video', [VideoController::class, 'store']);

//Rookies public carousel
Route::get('public/rookies', [RookieCarouselController::class, 'getPublicRookies']);
//Leaders public carousel
Route::get('public/leaders', [LeaderController::class, 'getPublicLeaders']);
//Rookie's public show
Route::get('public/rookie/{username}', [RookieController::class, 'showPublicRookie']);
//User's signup attempt
Route::get('public/signup-attempt', [UserController::class, 'signupAttempt']);

//Goal's public show
Route::get('public/goals/{goal}', [RookieGoalController::class, 'publicShow']);

//Discord Auth
Route::post('discord/auth', [UserController::class, 'discordAuth']);

Route::group(['middleware' => 'ccbill'], function () {
    Route::post('ccbill/webhook', [CCbillWebhook::class, 'store']);
});

Route::group(['middleware' => 'sendgrid'], function() {
    Route::post('sendgrid/webhook', [SendgridWebhook::class, 'sendgridWebhook']);
});

Route::post('transcoder/webhook', [ElasticTranscoderWebhook::class, 'store']);

Route::post('telegram', [TelegramWebhook::class, 'store']);


//Unsubscribe
Route::get('profile/unsubscribe/{token}', [UserController::class, 'unsubscribe']);


Route::group(['middleware' => ['auth:api', 'untrusted', 'last_activity', 'active']], function () {

    Route::group(['prefix' => 'mixpanel'], function () {
        Route::post('/events', [MixpanelController::class, 'storeEvent']);
    });

    Route::group(['prefix' => 'referral-email'], function () {
        Route::post('/', [UserReferralController::class, 'referLeader']);
    });

    Route::post('telegram/disconnect', [UserController::class, 'disconnectTelegramBot']);

    Route::group(['prefix' => 'identity-documents'], function () {
        Route::post('/', [IdentityVerifyController::class, 'verify']);
    });

    Route::group(['prefix' => 'photo'], function () {
        Route::post('/assign', [PhotoController::class, 'assignPhoto']);
        Route::get('/', [PhotoController::class, 'index']);

        Route::group(['prefix' => '{photo}'], function () {
            Route::post('/delete', [PhotoController::class, 'deletePhoto']);
        });

        Route::group(['prefix' => '{photoHistory}'], function () {
            Route::post('/delete/validation', [PhotoController::class, 'deleteValidationPhoto']);
        });
    });

    Route::group(['prefix' => 'profile'], function () {
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/check-password', [AuthController::class, 'checkCurrentPassword']);
        Route::post('/update', [UserController::class, 'update']);
        Route::post('/avatar', [UserController::class, 'setAvatar']);
        Route::delete('/avatar', [UserController::class, 'removeAvatar']);
    });

    Route::group(['prefix' => 'chat'], function () {
        Route::post('init', [PubnubChannelController::class, 'init']);
        Route::post('report', [ComplaintController::class, 'store']);
        Route::get('report-categories', [ComplaintController::class, 'indexTypes']);
        Route::get('messages', [PubnubChannelController::class, 'index']);

        Route::group(['prefix' => 'attachments'], function () {
            Route::post('send_{type}', [ChatAttachmentController::class, 'store']);
            Route::prefix('/{chatAttachment}')->group(function () {
                Route::get('/', [ChatAttachmentController::class, 'show']);
            });
        });
    });

    Route::get('/random-avatars', [UserController::class, 'getRandomAvatars']);

    Route::get('/transactions', [TransactionController::class, 'index']);

    Route::get('/notifications/', [NotificationController::class, 'index']);
    Route::get('/notifications/last-read', [NotificationController::class, 'lastRead']);

    Route::post('/notifications/seen', [NotificationController::class, 'seen']);
    Route::group(['prefix' => 'channels'], function () {

        Route::group(['prefix' => '{pubnubChannel}'], function () {
            Route::post('/messages-ping', [PubnubMessagesController::class, 'store']);

            Route::prefix('channels_reads_timetokens')->group(function () {
                Route::post('/', [ChannelReadTimetokenController::class, 'updateOrCreate']);
            });
        });
    });

    Route::group(['middleware' => 'rookie'], function () {

        Route::group(['prefix' => 'channels'], function () {

            Route::group(['prefix' => '{pubnubChannel}'], function () {
                Route::post('/pause', [PubnubChannelController::class, 'pause']);
                Route::post('/resume', [PubnubChannelController::class, 'resume']);
            });
        });


        Route::group(['prefix' => 'profile'], function () {
            Route::post('/win-seen', [RookieController::class, 'seenRookieWin']);
            Route::post('/converters', [RookiesConverterRequestController::class, 'store']);
            Route::patch('/converters/{rookiesConverterRequest}', [RookiesConverterRequestController::class, 'update']);
        });

        Route::group(['prefix' => 'contents'], function () {
            Route::get('/inspiration', [ContentEditorController::class, 'getInspirationContents']);
            Route::get('/news-update', [ContentEditorController::class, 'getNewsUpdateContents']);
        });

        Route::group(['prefix' => 'rookie'], function () {
            Route::get('/daily', [RookieWinnerHistoryController::class, 'rookieOfTheDay']);
            Route::get('/winners', [RookieWinnerHistoryController::class, 'morgiWinners']);
        });

        Route::get('/score', [RookieScoreController::class, 'profilePerformance']);

        Route::get('leaders/list', [LeaderController::class, 'leadersList']);

        Route::group(['prefix' => 'leader'], function () {
            Route::get('/', [LeaderController::class, 'chatIndex']);
            Route::group(['prefix' => '/{leader}'], function () {
                Route::get('/', [LeaderController::class, 'show']);
                Route::post('/nickname', [LeaderController::class, 'setNickname']);
                Route::patch('/nickname/{nickname}', [LeaderController::class, 'updateNickname']);
                Route::delete('/nickname/{nickname}', [LeaderController::class, 'removeNickname']);
            });
            Route::group(['prefix' => '/{user}'], function () {
                Route::post('/block', [UserBlockController::class, 'store']);
                Route::delete('/block', [UserBlockController::class, 'delete']);
            });
        });

        Route::post('video/assign', [VideoController::class, 'assign']);

        Route::group(['prefix' => 'transactions'], function () {
            Route::get('/micromorgi/{leader}', [BalanceTransactionController::class, 'rookieMicroMorgiTransactionsFromLeader']); //OLD /transactions/history/{leader_id}
            Route::get('/morgi', [BalanceTransactionController::class, 'rookieMorgiTransactions']); //OLD /transactions
            Route::get('/morgi/{leader}', [BalanceTransactionController::class, 'rookieMorgiTransactionsFromLeader']); //OLD transactions/{leader}
        });


        Route::group(['prefix' => 'payments'], function () {
            Route::get('/', [PaymentController::class, 'index']);
            Route::post('/', [PaymentController::class, 'store']);
            Route::get('/platforms', [PaymentController::class, 'platforms']);

            Route::group(['prefix' => '/{paymentPlatformRookie}'], function () {
                Route::get('/', [PaymentController::class, 'show']);
                Route::delete('/', [PaymentController::class, 'delete']);
                Route::post('/', [PaymentController::class, 'update']);
            });
        });

        // Goals

        Route::group(['prefix' => 'goals'], function () {
            Route::post('/', [RookieGoalController::class, 'store']);
            Route::get('/types', [GoalTypeController::class, 'index']);
            Route::group(['prefix' => '{goal}'], function () {
                Route::get('/transactions', [TransactionController::class, 'goalTransactions']);
                Route::post('/request-review', [RookieGoalController::class, 'requestReview']);
                Route::post('/cancel', [RookieGoalController::class, 'cancel']);
                Route::post('/retrieve', [RookieGoalController::class, 'withdraw']);
                Route::patch('/', [RookieGoalController::class, 'update']);
                Route::delete('/', [RookieGoalController::class, 'delete']);
                Route::group(['prefix' => 'proofs'], function () {
                    Route::post('/', [RookieGoalController::class, 'submitProof']);
                    Route::delete('/{proof}', [RookieGoalController::class, 'removeProof']);
                });
                Route::get('/supporters', [RookieGoalController::class, 'supporters']);
            });

        });

        // Broadcasts
        Route::group(['prefix' => 'broadcasts'], function () {
            Route::post('/', [BroadcastController::class, 'store']);
            Route::get('/', [BroadcastController::class, 'index']);
            Route::group(['prefix' => '{broadcast}'], function () {
                Route::get('/', [BroadcastController::class, 'show']);
                //Route::patch('/', [BroadcastController::class, 'update']);
                //Route::delete('/', [BroadcastController::class, 'delete']);
                Route::post('/messages', [BroadcastController::class, 'sendMessage']);
            });
        });

    });

    Route::group(['prefix' => 'rookies'], function () {
        Route::group(['prefix' => '{rookie}'], function () {
            Route::group(['prefix' => 'goals'], function () {
                Route::get('/past', [RookieGoalController::class, 'indexPastGoals']);
                Route::get('/', [RookieGoalController::class, 'index']);
                Route::get('/{goal}', [RookieGoalController::class, 'show']);
            });
        });
    });

    Route::group(['middleware' => 'leader'], function () {

        Route::post('/refer-rookie', [UserReferralController::class, 'referRookie']);

        Route::get('/coupons/transactions', [CouponController::class, 'indexTransactions']);
        Route::get('/givebacks', [GivebackController::class, 'index']);
        Route::post('/actions-tracking/{rookie}', [ActionTrackingController::class, 'store']);
        Route::get('/paths/unlocked', [PathController::class, 'getUnlockedPaths']);
        Route::get('/paths/locked', [PathController::class, 'getLockedPaths']);
        Route::get('/latest-payment', [LeaderController::class, 'latestPayment']);
        Route::post('/credit-card', [CcbillController::class, 'addCreditCard']);

        Route::group(['prefix' => 'morgi'], function () {
            Route::get('/list', [SubscriptionController::class, 'indexPackages']);
            Route::get('/active-gifting', [LeaderController::class, 'activeMorgiGifting']);
        });

        Route::group(['prefix' => 'micromorgi'], function () {
            Route::get('/list', [MicroMorgiController::class, 'indexPackages']);
            Route::get('/transactions', [BalanceTransactionController::class, 'leaderMicroMorgiTransactions']);
            Route::post('/send/{rookie}/{amount}', [MicroMorgiController::class, 'sendMicromorgi']);
            Route::post('/buy/{micromorgiPackage}', [MicroMorgiController::class, 'buyMicromorgi']);
        });

        Route::group(['prefix' => 'rookies'], function () {
            Route::get('/birthday', [RookieCarouselController::class, 'rookiesTodayBirthdays']);
            Route::get('/location/{country}', [RookieCarouselController::class, 'getRookiesByCountry']);
            Route::get('/saved', [RookieSavedController::class, 'getRookiesSaved']);
            Route::get('/', [RookieCarouselController::class, 'getRookies']);
            Route::get('/second-chance', [RookieCarouselController::class, 'getRookiesSeen']);

            //TODO refactorare sul v2
            Route::group(['prefix' => '/{rookie}'], function () {
                Route::group(['prefix' => '/goals'], function () {
                    Route::group(['prefix' => '/{goal}'], function () {
                        Route::post('/donate', [LeaderGoalController::class, 'donate']);
                    });
                });
            });
        });

        Route::group(['prefix' => 'subscriptions'], function () {
            Route::post('/renew', [CcbillController::class, 'renewSubscriptions']);
            Route::get('/renew', [CcbillController::class, 'indexToRenew']);
            Route::get('/renew/credit-cards', [CcbillController::class, 'indexToRenewWithCc']);
        });

        Route::group(['prefix' => 'rookie'], function () {

            Route::group(['prefix' => '/{user}'], function () {
                Route::post('/block', [UserBlockController::class, 'store']);
                Route::delete('/block', [UserBlockController::class, 'delete']);
            });

            Route::group(['prefix' => '/{identifier}'], function () {
                Route::get('/', [RookieController::class, 'showRookie']);
            });

            Route::group(['prefix' => '/{rookie}'], function () {
                Route::get('/morgi', [BalanceTransactionController::class, 'morgiGivenToRookie']);
                Route::get('/micromorgi', [BalanceTransactionController::class, 'microMorgiGivenToRookie']);

                Route::post('/save', [RookieSavedController::class, 'saveRookie']);
                Route::post('/unsave', [RookieSavedController::class, 'unsaveRookie']);
                //Route::post('/seen', [LeaderController::class, 'seenRookie']);

                Route::post('/open-channel', [PubnubChannelController::class, 'store']);

                Route::group(['prefix' => '/gift'], function () {
                    Route::post('/', [SubscriptionController::class, 'store']); //OLD gift/rookie/{rookie}
                    Route::delete('/', [SubscriptionController::class, 'delete']); //OLD gift/rookie/{rookie}
                });
            });
        });

        Route::group(['prefix' => 'quotations'], function (){
            Route::get('/', [LeaderQuotationController::class, 'index']);
            Route::post('/create', [LeaderQuotationController::class, 'create']);
            Route::post('/update', [LeaderQuotationController::class, 'update']);
        });

        //TODO refactorare sul v2
        Route::group(['prefix' => 'goals'], function (){
            Route::get('/', [LeaderGoalController::class, 'index']);
            Route::get('/supported', [LeaderGoalController::class, 'supportedGoals']);
            Route::get('/saved', [SavedGoalController::class, 'index']);
            Route::get('/paths', [LeaderGoalController::class, 'getPaths']);
            Route::post('/{goal}/save', [SavedGoalController::class, 'store']);
            Route::delete('/{goal}/save', [SavedGoalController::class, 'unSave']);
        });
    });

    Route::group(['prefix' => 'goals'], function (){
        Route::get('/{goal}', [LeaderGoalController::class, 'show']);
    });

});
