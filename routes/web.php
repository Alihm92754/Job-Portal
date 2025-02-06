<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
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