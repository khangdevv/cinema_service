<?php

use App\Http\Controllers\Api\MovieController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AccountController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/info', [AuthController::class, 'getInfo']);

    Route::put('/accounts/{account}', [AccountController::class, 'update']);
    Route::get('/movies', [MovieController::class, 'index']);
    Route::get('/movies/{movie}', [MovieController::class, 'show']);

    Route::middleware('role:ADMIN')->group(function () {
        Route::apiResource('/accounts', AccountController::class);
        Route::apiResource('/movies', MovieController::class)
             ->except(['index', 'show']);
    });
});






