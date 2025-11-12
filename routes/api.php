<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AccountController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/info', [AuthController::class, 'getInfo']);

    Route::put('/update', [AccountController::class, 'update']);

    Route::middleware('role:ADMIN')->group(function () {
        Route::apiResource('/admin/accounts', AccountController::class);
    });
});






