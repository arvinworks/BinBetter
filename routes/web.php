<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

// routes/web.php
Route::get('/dashboard', [HomeController::class, 'showDashboard']);

Route::get('/', [App\Http\Controllers\GuestController::class, 'welcome']);
Route::get('/get-municipalities', [App\Http\Controllers\Auth\RegisterController::class, 'getMunicipalities']);
Route::get('/get-barangays', [App\Http\Controllers\GuestController::class, 'getBarangays']);

Route::post('/login/ajax', [App\Http\Controllers\Auth\LoginController::class, 'ajaxLogin']);

Auth::routes(['verify' => true]);

Route::get('/secure-js-file/{filename}', [App\Http\Controllers\SecureController::class, 'serveJsFile'])->name('secure.js');
Route::post('/verify/code',  [App\Http\Controllers\Auth\VerificationController::class, 'otp_verify']);

Route::get('/google/redirect', [App\Http\Controllers\GoogleLoginController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/google/callback', [App\Http\Controllers\GoogleLoginController::class, 'handleGoogleCallback'])->name('google.callback');

Route::get('/pet-details/{name}/{id}', [App\Http\Controllers\HomeController::class, 'pet_details'])->name('pet.details');

Route::middleware(['prevent-back-history', 'auth', 'verified'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/post-report', [App\Http\Controllers\HomeController::class, 'post_report_api']);
    Route::get('/post-recycled', [App\Http\Controllers\HomeController::class, 'post_recycled_api']);
    Route::post('/post-destroy', [App\Http\Controllers\HomeController::class, 'post_destroy']);
    Route::get('/analytics', [App\Http\Controllers\HomeController::class, 'analytics_index'])->name('analytics');

    Route::get('/view-notification', [App\Http\Controllers\HomeController::class, 'view_notification'])->name('viewnotification.index');
    Route::get('/view-notification-api', [App\Http\Controllers\HomeController::class, 'view_notification_api'])->name('notification.api.index');

    //General Setting
    Route::get('/generalsettings', [App\Http\Controllers\GeneralSettingsController::class, 'index'])->name('generalsettings');
    Route::post('/generalsettings-company', [App\Http\Controllers\GeneralSettingsController::class, 'company']);
    Route::post('/generalsettings-profile', [App\Http\Controllers\GeneralSettingsController::class, 'profile']);
    Route::post('/generalsettings-account', [App\Http\Controllers\GeneralSettingsController::class, 'account']);
    Route::post('/generalsettings-password', [App\Http\Controllers\GeneralSettingsController::class, 'password']);
    Route::post('/account/remove', [App\Http\Controllers\GeneralSettingsController::class, 'removeAccount'])->name('account.remove');

    Route::resource('message', App\Http\Controllers\MessagesController::class);

    Route::resource('lgu', App\Http\Controllers\UserMngtLGUController::class);
    Route::post('/lgu/enable',  [App\Http\Controllers\UserMngtLGUController::class, 'enable']);
    Route::post('/lgu/disable',  [App\Http\Controllers\UserMngtLGUController::class, 'disable']);


    Route::resource('ngo', App\Http\Controllers\UserMngtNGOController::class);
    Route::post('/ngo/enable',  [App\Http\Controllers\UserMngtNGOController::class, 'enable']);
    Route::post('/ngo/disable',  [App\Http\Controllers\UserMngtNGOController::class, 'disable']);

    Route::resource('resident', App\Http\Controllers\UserMngtResidentController::class);
    Route::post('/resident/enable',  [App\Http\Controllers\UserMngtResidentController::class, 'enable']);
    Route::post('/resident/disable',  [App\Http\Controllers\UserMngtResidentController::class, 'disable']);


    Route::resource('service', App\Http\Controllers\ServiceController::class);
    Route::resource('subscriptionsettings', App\Http\Controllers\SubscriptionSettingsController::class);

    Route::resource('postreport', App\Http\Controllers\PostReportController::class);
    Route::post('/postreport-accept', [App\Http\Controllers\PostReportController::class, 'accept_report']);

    Route::resource('garbage', App\Http\Controllers\GarbageCollectionController::class);


    Route::resource('garbagetip', App\Http\Controllers\GarbageTipsController::class);
    Route::get('garbagetip-comment-api',  [App\Http\Controllers\GarbageTipsController::class, 'comment_api']);
    Route::post('comment-garbagetip',  [App\Http\Controllers\GarbageTipsController::class, 'comment_garbagetip']);
    Route::post('/comment-garbagetip/{action}/{commentId}', [App\Http\Controllers\GarbageTipsController::class, 'garbagetip_handleLikeDislike']);

    Route::resource('report-garbagetip', App\Http\Controllers\ReportGarbageTipController::class);

    Route::resource('managereward', App\Http\Controllers\RewardManagementController::class);
    Route::resource('comment', App\Http\Controllers\CommentController::class);
    Route::post('/comment/{action}/{commentId}', [App\Http\Controllers\CommentController::class, 'handleLikeDislike']);

    Route::resource('gcash', App\Http\Controllers\GcashSettingsController::class);
    Route::post('gcash-switch', [App\Http\Controllers\GcashSettingsController::class, 'switch_status']);

    Route::resource('payment', App\Http\Controllers\PaymentController::class);
    Route::post('show-gcash', [App\Http\Controllers\PaymentController::class, 'show_gcash']);
    Route::post('/payment/receive', [App\Http\Controllers\PaymentController::class, 'receive']);
    Route::post('/payment/reject', [App\Http\Controllers\PaymentController::class, 'reject']);

    //Subscription
    Route::get('/subscription', [App\Http\Controllers\SubscriptionController::class, 'index'])->name('subscription');
    Route::get('/subscription-api', [App\Http\Controllers\SubscriptionController::class, 'subscription_api'])->name('subscription.api');
    Route::post('/subscription-select', [App\Http\Controllers\SubscriptionController::class, 'store_subscription']);
    Route::post('/subscription-cancel', [App\Http\Controllers\SubscriptionController::class, 'cancel_subscription']);
    Route::post('/subscription-approve', [App\Http\Controllers\SubscriptionController::class, 'approve_subscription']);
    Route::get('/subscription-reward', [App\Http\Controllers\SubscriptionController::class, 'subscription_reward'])->name('subscription.reward');
    Route::post('/subscription-claim', [App\Http\Controllers\SubscriptionController::class, 'subscription_claim_reward'])->name('subscription.claimreward');

    //NGO
    Route::resource('event', App\Http\Controllers\EventController::class);
    Route::get('event-ngo', [App\Http\Controllers\EventController::class, 'event_ngo']);
    Route::post('/join-event', [App\Http\Controllers\EventController::class, 'join_event'])->name('event.join');
    Route::get('/event-attendance', [App\Http\Controllers\EventController::class, 'event_attendance'])->name('event.attendance');
    Route::get('/event-attendance-api', [App\Http\Controllers\EventController::class, 'event_attendance_api']);
    Route::post('/event-generate-qr', [App\Http\Controllers\EventController::class, 'generate_qr']);
    Route::get('/event-scan/{jeid}/{userid}/{eventid}', [App\Http\Controllers\EventController::class, 'event_scan_attendance'])->name('event.scan');
});
