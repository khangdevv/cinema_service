<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AccountController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/info', [AuthController::class, 'getInfo']);

    // Route::middleware('role:ADMIN')->group(function () {
    //     Route::get('/admin/accounts', [AccountController::class, 'index']);
    //     Route::get('/admin/accounts/{id}', [AccountController::class, 'show']);
    //     Route::put('/admin/accounts/{id}', [AccountController::class, 'update'] );
    // });
});

Route::apiResource('accounts', AccountController::class);


