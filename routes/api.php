<?php

use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\ScreenController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SeatController;
use App\Http\Controllers\Api\ShowtimeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AccountController;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {

     Route::post('/logout', [AuthController::class, 'logout']);
     Route::get('/info', [AuthController::class, 'getInfo']);

     Route::put('/accounts/{account}', [AccountController::class, 'update']);
     Route::get('/movies', [MovieController::class, 'index']);
     Route::get('/movies/{movie}', [MovieController::class, 'show']);
     Route::get('/screens', [ScreenController::class, 'index']);
     Route::get('/screens/{screen}', [ScreenController::class, 'show']);
     Route::get('/products', [ProductController::class, 'index']);
     Route::get('/products/{product}', [ProductController::class, 'show']);
     Route::get('/seats', [SeatController::class, 'index']);
     Route::get('/seats/{seat}', [SeatController::class, 'show']);
     Route::get('/showtimes', [ShowtimeController::class, 'index']);
     Route::get('/showtimes/{showtime}', [ShowtimeController::class, 'show']);
     Route::get('/showtimes/{showtime}/seat-map', [ShowtimeController::class, 'getSeatMap']);

     Route::get('/hello', function () {
          return response()->json(['msg' => 'Hello from API']);
     });

     Route::middleware('role:ADMIN')->group(function () {
          Route::apiResource('/accounts', AccountController::class);
          Route::apiResource('/movies', MovieController::class)
               ->except(['index', 'show']);
          Route::apiResource('/screens', ScreenController::class)
               ->except(['index', 'show']);
          Route::apiResource('/products', ProductController::class)
               ->except(['index', 'show']);
          Route::apiResource('/seats', SeatController::class)
               ->except(['index', 'show']);
          Route::post('/screens/{screen}/generate', [SeatController::class, 'generateSeats']);
          Route::apiResource('/showtimes', ShowtimeController::class)
               ->except(['index', 'show']);
          Route::post('/showtimes/generate', [ShowtimeController::class, 'generateShowtimes']);
          Route::post('/generateSchedule', [MovieController::class, 'generateSchedule']);
     });
});






