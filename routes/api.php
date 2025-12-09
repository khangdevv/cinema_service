<?php

use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\ScreenController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SeatController;
use App\Http\Controllers\Api\ShowtimeController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderLineController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AccountController;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/movies', [MovieController::class, 'index']);
Route::get('/movies/{movie}', [MovieController::class, 'show']);
Route::get('/screens', [ScreenController::class, 'index']);

// Webhook không cần auth
Route::post('/payment/webhook', [OrderController::class, 'webhook']);

Route::middleware('auth:sanctum')->group(function () {
     Route::post('/logout', [AuthController::class, 'logout']);
     Route::get('/info', [AuthController::class, 'getInfo']);
     Route::put('/accounts/{account}', [AccountController::class, 'update']);

     Route::get('/screens/{screen}', [ScreenController::class, 'show']);
     Route::get('/products', [ProductController::class, 'index']);
     Route::get('/products/{product}', [ProductController::class, 'show']);
     Route::get('/seats', [SeatController::class, 'index']);
     Route::get('/seats/{seat}', [SeatController::class, 'show']);
     Route::get('/showtimes', [ShowtimeController::class, 'index']);
     Route::get('/showtimes/{showtime}', [ShowtimeController::class, 'show']);
     Route::get('/showtimes/{showtime}/seat-map', [ShowtimeController::class, 'getSeatMap']);

     // Order routes
     Route::post('/orders', [OrderController::class, 'store']);
     Route::get('/orders', [OrderController::class, 'index']);
     Route::get('/orders/{id}', [OrderController::class, 'show']);
     Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);
     Route::get('/orders/{id}/qr', [OrderController::class, 'generateQR']);
     Route::post('/orders/{id}/confirm-payment', [OrderController::class, 'confirmPayment']);
     
     // Seat locking routes
     Route::post('/seat-locks', [OrderController::class, 'lockSeats']);
     Route::delete('/seat-locks', [OrderController::class, 'unlockSeats']);

     // OrderLine routes
     Route::get('/order-lines', [OrderLineController::class, 'index']);
     Route::get('/order-lines/{orderLine}', [OrderLineController::class, 'show']);
     Route::get('/orders/{orderId}/order-lines', [OrderLineController::class, 'getByOrder']);
     Route::get('/order-lines/statistics', [OrderLineController::class, 'statistics']);

     Route::get('/hello', function () {
          return response()->json(['msg' => 'Hello from API']);
     });

     Route::middleware('role:ADMIN')->group(function () {
          Route::apiResource('/accounts', AccountController::class);
          Route::apiResource('/movies', MovieController::class)
               ->except(['index', 'show']);

          Route::apiResource('/products', ProductController::class)
               ->except(['index', 'show']);
          Route::apiResource('/seats', SeatController::class)
               ->except(['index', 'show']);
          Route::apiResource('/order-lines', OrderLineController::class)
               ->except(['index', 'show']);
          Route::post('/screens/{screen}/generate', [SeatController::class, 'generateSeats']);
          Route::apiResource('/showtimes', ShowtimeController::class)
               ->except(['index', 'show']);
          Route::post('/showtimes/generate', [ShowtimeController::class, 'generateShowtimes']);
          Route::post('/movies/generateSchedule', [MovieController::class, 'generateSchedule']);
     });
});






