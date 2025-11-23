<?php

use App\Http\Controllers\Api\V1\PointsLedgerController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/my-points-history', [PointsLedgerController::class, 'myPointsHistory'])->name('my-points-history');
});
