<?php

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\Api\PingController;
use App\Http\Controllers\Api\UploadReplayController;
use App\Http\Controllers\Api\UserDetailController;
use Illuminate\Support\Facades\Route;

Route::post('/login', LoginController::class)->middleware('guest');
Route::get('/ping', PingController::class);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/replay', UploadReplayController::class);
    Route::get('/me', UserDetailController::class);
    Route::post('/logout', LogoutController::class);
});
