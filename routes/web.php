<?php

use App\Http\Controllers\Admin\AdminCouponController;
use App\Http\Controllers\Admin\Api\AdminApiCouponController;
use App\Http\Controllers\Admin\PubnubChannelSettingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserTabsController;
use App\Http\Controllers\Admin\MainController;
use App\Http\Controllers\Admin\UserComplaintsController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ComplianceController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UploadController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\Api\AdminApiSubscriptionController;
use App\Http\Controllers\Admin\UserStatusController;
use App\Http\Controllers\Admin\UserUpdateController;
use App\Http\Controllers\Admin\GoalController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::domain(env('ADMIN_DOMAIN'))->group(function () {

    Route::get('/login', [MainController::class, 'loginPage']);

    Route::middleware(['admin_logged'])->group(function () {
        Route::get('/', [MainController::class, 'homePage'])->name('index');

        Route::group(['prefix' => 'rookies'], function () {
            Route::get('/all', [UserController::class, 'getUsersByType'])->name('rookies.get');

            Route::get('/accepted', [UserController::class, 'getUsersByType'])->name('rookies.accepted');

            Route::get('/pending', [UserController::class, 'getUsersByType'])->name('rookies.pending');

            Route::get('/new_accounts', [UserController::class, 'getUsersByType'])->name('rookies.new_accounts');

            Route::get('/updated_accounts', [UserController::class, 'getUsersByType'])->name('rookies.updated_accounts');

            Route::get('/pending_id_verification', [UserController::class, 'getUsersByType'])->name('rookies.id_verification');

            Route::get('/rejected_accounts', [UserController::class, 'getUsersByType'])->name('rookies.rejected');

            Route::get('/blocked_accounts', [UserController::class, 'getUsersByType'])->name('rookies.blocked');

            Route::get('/updated_username', [UserController::class, 'getUsersByType'])->name('rookies.updated_username');

            Route::get('/favourite_rookies', [UserController::class, 'getUsersByType'])->name('rookies.favourite_rookies');

        });

        Route::group(['prefix' => 'leaders'], function () {

            Route::get('/all', [UserController::class, 'getUsersByType'])->name('leaders.get');

            Route::get('/accepted', [UserController::class, 'getUsersByType'])->name('leaders.accepted');

            Route::get('/pending_id_verification', [UserController::class, 'getUsersByType'])->name('leaders.id_verification');

            Route::get('/updated_accounts', [UserController::class, 'getUsersByType'])->name('leaders.updated');

            Route::get('/blocked_accounts', [UserController::class, 'getUsersByType'])->name('leaders.blocked');

            Route::get('/rejected_accounts', [UserController::class, 'getUsersByType'])->name('leaders.rejected');

            Route::get('/updated_username', [UserController::class, 'getUsersByType'])->name('leaders.updated_username');

        });

        Route::group(['prefix' => 'search_user'], function () {

            //user_profile
            Route::get('/', [UserController::class, 'getUserByData'])->name('user.index');

            Route::group(['prefix' => '/{id}/edit'], function () {

                //Profile page
                Route::group(['prefix' => '/profile_info'], function () {
                    Route::get('/', [UserController::class, 'editUserProfile'])->name('user.edit');

                    Route::post('/block_user', [UserController::class, 'blockUserById'])->name('user.edit.block');
                    Route::get('/re-active', [UserController::class, 'reActiveUserById'])->name('user.edit.re-active');
                    Route::post('/add-note', [UserController::class, 'addNoteToUser'])->name('user.edit.add-note');
                    Route::post('/edit-is-favourite', [UserController::class, 'editIsFavourite'])->name('user.edit.edit-is-favourite');
                    Route::post('/change-status', [UserController::class, 'updateStatus'])->name('user.edit.update-status');
                    Route::post('/action-verification-id', [UserController::class, 'actionDocumentVerification'])->name('user.edit.edit-verification-id');
                    Route::post('/reset-payment', [UserController::class, 'resetRookiePayment'])->name('user.edit.reset-payment');
                    Route::post('/update-birth-date', [UserController::class, 'updateUserBirthDate'])->name('user.edit.birth-date');
                    Route::post('/update-username', [UserController::class, 'updateUsername'])->name('user.edit.username');
                    Route::post('/update-spender-category', [UserController::class, 'updateSpenderCategory'])->name('user.edit.update-spender-category');
                    Route::post('/update-rookie-first-last-name', [UserController::class, 'updateFirstNameAndLastName'])->name('user.edit.update-first-last-name');
                    Route::post('/update-score', [UserController::class, 'updateBeautyIntelligenceLikelyScore'])->name('user.edit.update-score');
                    Route::post('/action-to-username', [UserController::class, 'actionToUsername'])->name('user.edit.action-to-username');
                });

                Route::get('/active_gifts', [SubscriptionController::class, 'showSubscriptions'])->name('user.edit.active_gifts');
                Route::get('/not_active_gifts', [SubscriptionController::class, 'showSubscriptions'])->name('user.edit.active_gifts.not-active-gifts');

                Route::get('/micromorgi', [UserTabsController::class, 'getMicromorgi'])->name('user.edit.micromorgi');
                Route::post('/micromorgi', [UserTabsController::class, 'addBonusMicroMorgi'])->name('user.edit.micromorgi.add_bonus');

                Route::get('/transactions', [UserTabsController::class, 'getTransactions'])->name('user.edit.transactions');

                Route::get('/activity_log', [UserTabsController::class, 'getActivityLog'])->name('user.edit.activity_log');

                Route::get('/complaints', [UserTabsController::class, 'getComplaints'])->name('user.edit.complaints');

                Route::group(['prefix' => '/related_accounts'], function () {

                    Route::get('/', [UserTabsController::class, 'getRelatedAccount'])->name('user.edit.related_account');

                    Route::get('/rookie', [UserTabsController::class, 'getRookieRelatedAccounts'])->name('user.edit.related_account.rookie');

                    Route::get('/leader', [UserTabsController::class, 'getLeaderRelatedAccounts'])->name('user.edit.related_account.leader');
                });

                Route::get('/CGB_history', [UserTabsController::class, 'getCGBHistory'])->name('user.edit.cgb_history');

                Route::get('/payment_history', [UserTabsController::class, 'getPaymentHistory'])->name('user.edit.payment-history');

                Route::post('/reset-password-link', [UserController::class, 'sendResetPasswordLink'])->name('user.edit.sent-password-reset');

                Route::post('/fine-user', [TransactionController::class, 'fineUser'])->name('user.edit.fine-user');

            });

            Route::group(['prefix' => '/{user}'], function () {
                Route::get('/coupons', [AdminCouponController::class, 'viewLeaderCoupons'])->name('view.user.coupons');
            });

        });

        Route::group(['prefix' => '/leaders'], function() {
            Route::group(['prefix' => '/{leader}'], function() {
                Route::group(['prefix' => '/coupons'], function() {
                    Route::post('/', [AdminCouponController::class, 'storeBonusCoupon'])->name('leader.coupon.store');
                });
            });
        });


        Route::group(['prefix' => 'user_complaints'], function () {
            Route::get('/', [UserComplaintsController::class, 'getUserComplaints'])->name('complaints.get');
            Route::post('/v1/complaints', [UserComplaintsController::class, 'getUserComplaintsApi'])->name('api.complaints');
            Route::get('/open', [UserComplaintsController::class, 'getUserComplaints'])->name('complaints.open.get');
            Route::get('/closed', [UserComplaintsController::class, 'getUserComplaints'])->name('complaints.closed.get');
            Route::get('/follow_up', [UserComplaintsController::class, 'getUserComplaints'])->name('complaints.follow-up.get');

            Route::group(['prefix' => '{id}'], function () {

                Route::get('/report_info', [UserComplaintsController::class, 'editUserComplaint'])->name('complaints.edit.get');
                Route::post('/report_info', [UserComplaintsController::class, 'editUserComplaint'])->name('complaints.edit.post');
                Route::get('/complaint_history', [UserComplaintsController::class, 'showUserComplaintHistory'])->name('complaints.show-history.get');
                Route::post('/report_info/create-note', [UserComplaintsController::class, 'createComplaintNote'])->name('complaint.edit.add-note');
            });

        });

        Route::group(['prefix' => 'compliance'], function (){

            Route::get('/chargeback_reports', [ComplianceController::class, 'getChargebackReports'])->name('chargeback_reports');
            Route::get('/refunds_reports', [ComplianceController::class, 'getRefundReports'])->name('refund_reports');

            Route::group(['prefix' => '/1-3-transactions'], function(){
                Route::get('/', [ComplianceController::class, 'getFirstThreeTransactions'])->name('three_transactions');
                Route::post('/action', [ComplianceController::class, 'internalActionToTransaction'])->name('action-transaction');
                Route::group(['prefix' => '/{user_id}'], function(){
                    Route::get('/', [ComplianceController::class, 'getFirstThreeTransactionsById'])->name('three_transactions_by_id');
                    Route::post('/', [ComplianceController::class, 'getFirstThreeTransactionsById'])->name('three_transactions_by_id.post');

                });
            });

            Route::group(['prefix' => '/transactions'], function(){

                Route::get('/search', [TransactionController::class, 'search'])->name('transaction.search');

                Route::group(['prefix' => '{transaction_id}'], function(){
                    Route::get('/', [ComplianceController::class, 'getTransactionById'])->name('transaction.show');
                });

                Route::group(['prefix' => '{leader_payment_id}'], function(){
                    Route::get('/info', [TransactionController::class, 'show'])->name('transaction.show2');
                });
            });

            Route::group(['prefix' => '/transactions_refunds'], function(){
                Route::get('/all', [ComplianceController::class, 'showTransactionsRefunds'])->name('transactions_refunds');
                Route::get('/chargebacks', [ComplianceController::class, 'showTransactionsRefunds'])->name('transactions_refunds.chargeback');
                Route::get('/refund_by_biller', [ComplianceController::class, 'showTransactionsRefunds'])->name('transactions_refunds.refund_biller');
                Route::get('/refund_by_admin', [ComplianceController::class, 'showTransactionsRefunds'])->name('transactions_refunds.refund_admin');
                Route::get('/void', [ComplianceController::class, 'showTransactionsRefunds'])->name('transactions_refunds.void');
                Route::get('/rebill_declined', [ComplianceController::class, 'showTransactionsRefunds'])->name('transactions_refunds.rebill_declined');
            });

            Route::group(['prefix' => '/reports'], function(){
                Route::get('/daily', [ComplianceController::class, 'reportPage'])->name('reports.daily');
                Route::get('/multiple_leaders', [ComplianceController::class, 'reportPage'])->name('reports.multiple_leaders');
                Route::get('/one_leader', [ComplianceController::class, 'reportPage'])->name('reports.one_leader');
                Route::get('/inactive_communications', [ComplianceController::class, 'reportPage'])->name('reports.inactive_communications');
                Route::get('/new_card', [ComplianceController::class, 'reportPage'])->name('reports.new_card');
                Route::get('/status_change', [ComplianceController::class, 'reportPage'])->name('reports.status_change');
            });

            Route::group(['prefix' => '/refunds'], function(){
                Route::get('/pending', [ComplianceController::class, 'getPendingRefund'])->name('refunds.pending');
                Route::get('/approved', [ComplianceController::class, 'getApprovedRefund'])->name('refunds.approved');
                Route::get('/failed', [ComplianceController::class, 'getFailedRefund'])->name('refunds.failed');
            });

        });

        Route::group(['prefix' => 'content_editor'], function (){
            Route::get('/', [MainController::class, 'indexContentEditor'])->name('content_editor');
            Route::post('/', [MainController::class, 'indexContentEditor'])->name('content_editor.post');
            Route::post('/add', [MainController::class, 'createContentEditor'])->name('content_editor.create');
            Route::post('/update', [MainController::class, 'updateContentEditor'])->name('content_editor.update');
            Route::post('/delete', [MainController::class, 'deleteContentEditor'])->name('content_editor.delete');
        });

        Route::get('/rookie_winners', [MainController::class, 'showRookieOfTheDay'])->name('show.rookies_ofd');

        Route::group(['prefix' => '/rookie/payments'], function(){
            Route::get('/main_payments', [PaymentController::class, 'getMainPaymentReport'])->name('main-payment');
            Route::post('/main_payments', [PaymentController::class, 'getMainPaymentReport'])->name('main-payment.post');
            Route::get('/summary_payments', [PaymentController::class, 'getSummaryPaymentReport'])->name('summary-payment');
            Route::post('/summary_payments', [PaymentController::class, 'getSummaryPaymentReport'])->name('summary-payment.post');

            Route::group(['prefix' => '/payment_prev_report'], function(){
                Route::get('/', [PaymentController::class, 'getPaymentPrevPeriod'])->name('user-payment-prev');
                Route::post('/', [PaymentController::class, 'getPaymentPrevPeriod'])->name('user-payment-prev.post');
                Route::post('/approve_payments/{period_id}', [PaymentController::class, 'approvePayments'])->name('payments.approve-all-payments');
                Route::get('/approve_single_payment/{transaction_id}', [PaymentController::class, 'approvePayment'])->name('payments.approve-single-payment');
                Route::post('/reject', [PaymentController::class, 'rejectPayment'])->name('user-payment-prev.reject');
            });

            Route::get('/rejects_reports', [PaymentController::class, 'getPaymentRejectsReports'])->name('rejects_reports');
            Route::get('/payment_history', [PaymentController::class, 'getPaymentHistoryByData'])->name('user-payment-history');
            Route::post('/payment_history', [PaymentController::class, 'getPaymentHistoryByData'])->name('user-payment-history.post');
            Route::get('/{id}/download/{payment_platform_id}', [PaymentController::class, 'downloadPaymentFile'])->name('payments.show.download');
        });

        Route::post('/get_customerly_id', [UserComplaintsController::class, 'getCustomerlyId'])->name('getCustomerlyId.post');

        Route::group(['prefix' => '/bad_words'], function(){
            Route::get('/', [MainController::class, 'getBadWords'])->name('bad.words.get');
            Route::post('/', [MainController::class, 'addBadWord'])->name('add.bad.words.post');
            Route::post('/delete', [MainController::class, 'deleteBadWord'])->name('add.bad.words.delete');
        });

        //CTA's
        Route::get('/logout', [MainController::class, 'logout']);

        Route::group(['prefix' => '/admin/api'], function(){

            Route::group(['prefix' => '/user'], function() {
                Route::get('/subscription/payments/{leader_payment_id}', [UserTabsController::class, 'getSubscriptionPaymentHistory'])->name('subscription-payments');

                Route::get('/morgi/fines', [UserTabsController::class, 'getFines'])->name('api.get-fines');
            });

            Route::group(['prefix' => '/compliance'], function() {
                Route::post('/transactions_refunds', [ComplianceController::class, 'getTransactionsRefunds'])->name('api.transactions_refunds');
                Route::post('/reports', [ComplianceController::class, 'getReports'])->name('api.reports');
            });

            Route::group(['prefix' => '/leaders'], function() {
                Route::group(['prefix' => '/{leader}'], function() {
                    Route::group(['prefix' => '/coupons'], function() {
                        Route::get('/', [AdminApiCouponController::class, 'getLeaderCoupons'])->name('api.leader.coupon.get');
                    });
                });
            });

            Route::group(['prefix' => '/subscriptions'], function() {
                Route::get('/packages', [AdminApiSubscriptionController::class, 'getSubscriptionPackages'])->name('api.subscription.packages.get');
            });


        });

        Route::group(['prefix' => '/micromorgi-bonus'], function() {
            Route::get('/', [UploadController::class, 'index'])->name('showMicromorgiBonus.get');
            Route::post('/', [UploadController::class, 'index'])->name('uploadMicromorgiBonus.post');
            Route::post('/double-check', [UploadController::class, 'doubleCheck'])->name('double-check.post');
            Route::post('/send-bonus', [UploadController::class, 'send'])->name('test-route');
        });

        Route::group(['prefix' => '/subscriptions'], function() {
            Route::post('/end', [SubscriptionController::class, 'endSubscription'])->name('subscriptions.end');
        });

    });

    Route::group(['prefix' => '/export'], function(){
        Route::post('/transactions', [ComplianceController::class, 'exportTransactionRefund'])->name('export.transactions');
        Route::post('/reports', [ComplianceController::class, 'exportReport'])->name('api.reports.export');

    });


    Route::get('/chat/{reported}/{reported_by}', [UserComplaintsController::class, 'getChat'])->name('chat');


    //CTA's
    Route::post('/send_login', [MainController::class, 'login']);

    Route::middleware(['admin'])->group(function () {
        Route::get('/chat/messages', [UserTabsController::class, 'getMessages'])->name('messages.get');
    });

    Route::post('/transactions/refund-transaction', [TransactionController::class, 'refundTransaction'])->name('refund-transaction');

    Route::post('/check-username', [MainController::class, 'checkUsername'])->name('check-username');



    /** NEW ROUTES */

    Route::middleware(['admin_logged'])->group(function(){

        Route::group(['prefix' => 'users'], function(){


            Route::group(['prefix' => '{user}'], function(){

                Route::post('/approve', [UserStatusController::class, 'approveUser'])->name('users.user.approve');
                Route::post('/decline', [UserStatusController::class, 'declineUser'])->name('users.user.decline');

                Route::group(['prefix' => 'updates'], function(){

                    Route::post('/approve', [UserStatusController::class, 'approveAllUpdates'])->name('users.user.updates.approve');
                    Route::post('/decline', [UserStatusController::class, 'declineAllUpdates'])->name('users.user.updates.decline');

                    Route::group(['prefix' => 'description'], function(){

                        Route::post('/approve', [UserUpdateController::class, 'approveDescription'])->name('users.user.updates.description.approve');
                        Route::post('/decline', [UserUpdateController::class, 'declineDescription'])->name('users.user.updates.description.decline');
                        Route::post('/decline-current', [UserUpdateController::class, 'declineStoredDescription'])->name('users.user.updates.description.decline-current');
                    });
                });
            });
        });

        Route::group(['prefix' => 'photos'], function() {

            Route::group(['prefix' => '{photo}'], function(){

                Route::post('/decline', [UserUpdateController::class, 'declineStoredPhoto'])->name('photos.photo.decline');
            });

            Route::group(['prefix' => 'new'], function () {

                Route::group(['prefix' => '{photo_history}'], function () {

                    Route::post('/approve', [UserUpdateController::class, 'approvePhoto'])->name('photos-histories.photo_history.approve');
                    Route::post('/decline', [UserUpdateController::class, 'declinePhoto'])->name('photos-histories.photo_history.decline');
                });
            });
        });

        Route::group(['prefix' => 'videos'], function() {

            Route::group(['prefix' => '{video}'], function(){

                Route::post('/decline', [UserUpdateController::class, 'declineStoredVideo'])->name('videos.video.decline');
            });

            Route::group(['prefix' => 'new'], function () {

                Route::group(['prefix' => '{video_history}'], function () {

                    Route::post('/approve', [UserUpdateController::class, 'approveVideo'])->name('videos-histories.video_history.approve');
                    Route::post('/decline', [UserUpdateController::class, 'declineVideo'])->name('videos-histories.video_history.decline');
                });
            });
        });

        Route::group(['prefix' => 'goals'], function() {

            Route::get('/', [GoalController::class, 'index'])->name('goals.index');

            Route::group(['prefix' => '{goal}'], function() {

                Route::get('/', [GoalController::class, 'show'])->name('goals.show');
            });
        });

        Route::group(['prefix' => 'channels-settings'], function() {
            Route::get('/', [PubnubChannelSettingController::class, 'index'])->name('pubnub-channels-settings.get.index');
        });
    });
});
