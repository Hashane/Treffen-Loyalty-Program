<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Socialite;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/auth/google/redirect', function () {
    return Socialite::driver('google')->redirect();
});

Route::get('/auth/google/callback', function () {
    $user = Socialite::driver('google')->user();

    dd($user);
    // $user->token
});

Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
//    Route::post('auth/logout', [AuthController::class, 'logout']);
//    Route::get('/me', [AuthController::class, 'me']);
//    Route::get('/points', function (Request $request) {
//        return response()->json(['points' => $request->user()->points]);
//    });
});