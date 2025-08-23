<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WebPushController;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('jwt');
    Route::get('/me', [AuthController::class, 'me'])->middleware('jwt');
});

Route::prefix('push')->group(function () {
    Route::get('/public-key', [WebPushController::class, 'publicKey']);
    Route::post('/subscribe', [WebPushController::class, 'subscribe'])->middleware('jwt');
    Route::post('/unsubscribe', [WebPushController::class, 'unsubscribe'])->middleware('jwt');
    Route::post('/send-test', [WebPushController::class, 'sendTest'])->middleware('jwt');
});
