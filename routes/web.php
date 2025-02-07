<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostJobController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\donotAllowUserToMakePayment;
use App\Http\Middleware\isEmployer;
use App\Http\Middleware\isPremiumUser;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

Route::get('/', function () {
    return view('welcome');
});
 
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
 
    return redirect('/login');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::get('/register/seeker', [UserController::class, 'createSeeker'])->name('create.seeker');
Route::post('/register/seeker', [UserController::class, 'storeSeeker'])->name('store.seeker');

Route::get('/register/employer', [UserController::class, 'createEmployer'])->name('create.employer');
Route::post('/register/employer', [UserController::class, 'storeEmployer'])->name('store.employer');


Route::get('/login', [UserController::class, 'login'])->name('login');
Route::post('/login', [UserController::class, 'postLogin'])->name('login.post');

Route::post('/logout', [UserController::class, 'logout'])->name('logout');

Route::get('dashboard', [DashboardController::class, 'index'])->middleware('verified')->name('dashboard');

Route::get('resend/verification/email', [DashboardController::class, 'resend'])->name('resend.email');

Route::get('/verify', [DashboardController::class, 'verify'])->name('verification.notice');

Route::get('subscribe', [SubscriptionController::class, 'subscribe'])->name('subscribe')->middleware('auth', isEmployer::class, donotAllowUserToMakePayment::class);
Route::get('pay/weekly', [SubscriptionController::class, 'initiatePayment'])->name('pay.weekly')->middleware('auth');
Route::get('pay/monthly', [SubscriptionController::class, 'initiatePayment'])->name('pay.monthly')->middleware('auth');
Route::get('pay/yearly', [SubscriptionController::class, 'initiatePayment'])->name('pay.yearly')->middleware('auth');
Route::get('payment/success', [SubscriptionController::class, 'paymentSuccess'])->name('payment.success')->middleware('auth');
Route::get('payment/cancel', [SubscriptionController::class, 'cancel'])->name('payment.cancel')->middleware('auth');

Route::get('job/create', [PostJobController::class, 'create'])->name('job.create')->middleware(isPremiumUser::class);
