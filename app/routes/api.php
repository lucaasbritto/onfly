<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\TravelRequestController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);

    Route::prefix('requests')->group(function () {
        Route::get('/', [TravelRequestController::class, 'index']);
        Route::post('/', [TravelRequestController::class, 'store']);
    });
});
