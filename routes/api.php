<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('user', function (Request $request) {
        return $request->user()->load('roles');
    });

    Route::middleware('can:manage_users')->group(function(){
        Route::apiResource('users', UserController::class);
    });
});
