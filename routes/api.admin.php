<?php

use App\Http\Controllers\Admin\Api\AdminApiCarouselSettingsController;
use App\Http\Controllers\Admin\Api\AdminApiGoalProofController;
use App\Http\Controllers\Admin\Api\AdminApiPubnubChannelSettingController;
use App\Http\Controllers\Admin\Api\AdminApiConverterCarouselPositionController;
use App\Http\Controllers\Admin\Api\AdminApiSystemSettingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Api\AdminApiComplaintController;
use App\Http\Controllers\Admin\Api\AdminApiLeaderPaymentController;
use App\Http\Controllers\Admin\Api\AdminApiTransactionController;
use App\Http\Controllers\Admin\Api\AdminApiUserController;
use App\Http\Controllers\Admin\Api\AdminApiChatController;
use App\Http\Controllers\Admin\Api\AdminApiSubscriptionController;
use App\Http\Controllers\Admin\Api\AdminApiRookieController;
use App\Http\Controllers\Admin\Api\AdminApiGoalController;

/*
|--------------------------------------------------------------------------
| ADMIN API Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::domain(env('ADMIN_DOMAIN'))->middleware(['admin_logged'])->group(function () {

    Route::group(['prefix' => 'complaints'], function () {

        Route::post('/', [AdminApiComplaintController::class, 'getAllComplaints'])->name('api.admin.complaints');
    });

    Route::group(['prefix' => 'compliance'], function () {

        Route::get('/refunds', [AdminApiLeaderPaymentController::class, 'getTransactionRefunds'])->name('api.admin.compliance.refunds');
    });

    Route::group(['prefix' => 'transactions'], function () {

        Route::get('/{user}', [AdminApiTransactionController::class, 'getTransactionsByUser'])->name('api.admin.user.transaction.get');
    });

    Route::group(['prefix' => 'users'], function () {

        Route::group(['prefix' => '{user}'], function () {

            Route::get('/', [AdminApiUserController::class, 'getUserInfo'])->name('api.admin.user.get');

            Route::group(['prefix' => 'transactions'], function () {

                Route::group(['prefix' => 'micromorgi'], function () {

                    Route::get('/', [\App\Http\Controllers\Admin\RefactorWorkaround\RefactorUserMicromorgiController::class, 'getUserMicromorgi'])->name('api.admin.user.transaction.micromorgi.get');
                });
            });
        });

        Route::group(['prefix' => 'rookies'], function () {

            Route::get('/converters', [AdminApiRookieController::class, 'getConverters'])->name('api.admin.rookie.converters.get');
            Route::post('/converters-carousel-positions', [AdminApiRookieController::class, 'updateConvertersCarouselPosition'])->name('api.admin.rookie.converters.update-carousel-positions.post');

            Route::group(['prefix' => '{rookie}'], function () {

                Route::patch('/', [AdminApiRookieController::class, 'update'])->name('api.admin.rookie.update');
            });
        });
    });

    Route::group(['prefix' => 'chats'], function () {

        Route::get('/find-by-users', [AdminApiChatController::class, 'getChatData'])->name('api.admin.chats.find-by-users.get');
    });

    Route::group(['prefix' => 'subscriptions'], function () {

        Route::group(['prefix' => '{subscription}'], function () {

            Route::get('/payments-history', [AdminApiSubscriptionController::class, 'getSubscriptionHistory'])->name('api.admin.subscriptions.payments-history.get');
        });
    });

    Route::group(['prefix' => 'goals'], function () {

        Route::get('/', [AdminApiGoalController::class, 'index'])->name('api.admin.goals.index');

        Route::get('/available-actions', [AdminApiGoalController::class, 'getAllAvailableGoalStatusAction'])->name('api.admin.goals.available-actions');
        Route::get('/status-counter', [AdminApiGoalController::class, 'getGoalsStatusCounter'])->name('api.admin.goals.status-counter');

        Route::group(['prefix' => '{goal}'], function () {

            Route::get('/', [AdminApiGoalController::class, 'show'])->name('api.admin.goals.show');
            Route::post('/update-status', [AdminApiGoalController::class, 'updateStatus'])->name('api.admin.goals.update-status');

            Route::group(['prefix' => 'proofs'], function () {

                Route::group(['prefix' => '{goal_proof}'], function () {

                    Route::patch('/approve', [AdminApiGoalProofController::class, 'approveProof'])->name('api.admin.goals.proofs.approve');
                    Route::post('/decline', [AdminApiGoalProofController::class, 'declineProof'])->name('api.admin.goals.proofs.decline');
                });
            });
        });
    });

    Route::group(['prefix' => 'pubnub'], function () {

        Route::group(['prefix' => 'pubnub-channels-settings'], function () {

            Route::get('/', [AdminApiPubnubChannelSettingController::class, 'index'])->name('api.admin.pubnub.channels-settings.get');

            Route::group(['prefix' => '{pubnub_channel_setting}'], function () {

                Route::post('/', [AdminApiPubnubChannelSettingController::class, 'update'])->name('api.admin.pubnub.channels-settings.update.post');
            });

        });
    });

    Route::group(['prefix' => 'carousel-settings'], function () {

        Route::patch('/', [AdminApiCarouselSettingsController::class, 'update'])->name('api.admin.carousel-settings.update.patch');

        Route::get('/', [AdminApiCarouselSettingsController::class, 'index'])->name('api.admin.carousel-settings.index.get');
    }) ;

    Route::group(['prefix' => 'system-settings'], function () {

        Route::get('/system-settings-lists', [AdminApiSystemSettingController::class, 'getSystemOrders'])->name('api.admin.system-settings.list.get');
        Route::get('/current-system-order', [AdminApiSystemSettingController::class, 'getCurrentSystemOrder'])->name('api.admin.system-settings.current.get');

        Route::group(['prefix' => '{system_setting}'], function () {

            Route::post('/', [AdminApiSystemSettingController::class, 'updateCurrentSystemOrder'])->name('api.admin.system-settings.update-current.post');
        });
    });

    Route::group(['prefix' => 'converters-carousel-positions'], function () {

        Route::get('/', [AdminApiConverterCarouselPositionController::class, 'index'])->name('api.admin.converters-carousel-positions.index.get');
        Route::post('/', [AdminApiConverterCarouselPositionController::class, 'update'])->name('api.admin.converters-carousel-positions.update.post');
    });
});
