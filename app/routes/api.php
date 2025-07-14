<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\TravelRequestController;
use App\Http\Controllers\User\UserController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);

    Route::prefix('requests')->group(function () {
        Route::get('/', [TravelRequestController::class, 'index']);
        Route::post('/', [TravelRequestController::class, 'store']);
        Route::patch('/{id}', [TravelRequestController::class, 'updateStatus']);
    });

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/admins', [UserController::class, 'admins']);
});
