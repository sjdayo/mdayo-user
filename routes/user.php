<?php

use Illuminate\Support\Facades\Route;
use Mdayo\User\Http\Controllers\AuthController;
Route::prefix('user')->group(function(){
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('info', [AuthController::class, 'show']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});