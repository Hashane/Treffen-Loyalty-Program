<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\MemberController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('google.callback');

Route::get('/auth/facebook/redirect', [AuthController::class, 'redirectToFacebook'])->name('facebook.redirect');
Route::get('/auth/facebook/callback', [AuthController::class, 'handleFacebookCallback'])->name('facebook.callback');

Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:login')->name('login');

Route::post('auth/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('guest')->name('password.email');
Route::post('auth/reset-password', [AuthController::class, 'resetPassword'])->middleware('guest')->middleware('guest')->name('password.reset');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::patch('/profile', [MemberController::class, 'updateProfile']);
    Route::get('/oauth/connections', [AuthController::class, 'oauthConnections']);
    Route::delete('/oauth/connections/{oauth_connection}', [AuthController::class, 'unlinkOAuthProvider']);

    Route::post('/email/send-verification-code', [MemberController::class, 'sendVerificationCode'])
        ->middleware('throttle:5,1');
    Route::post('/email/verify', [MemberController::class, 'verifyEmail'])
        ->middleware('throttle:5,1');
});
