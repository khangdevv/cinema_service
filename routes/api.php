<?php

use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\ScreenController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SeatController;
use App\Http\Controllers\Api\ShowtimeController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AccountController;

// Quiz Controllers
use App\Http\Controllers\Quiz\AuthController as QuizAuthController;
use App\Http\Controllers\Quiz\ExamController;
use App\Http\Controllers\Quiz\TheoryController;
use App\Http\Controllers\Quiz\StatisticsController;

// =====================================================
// QUIZ API ROUTES
// =====================================================

// Public Quiz routes
Route::prefix('quiz')->group(function () {
    // Auth
    Route::post('/register', [QuizAuthController::class, 'register']);
    Route::post('/login', [QuizAuthController::class, 'login']);
    Route::get('/auth/google', [QuizAuthController::class, 'redirectToGoogle']);
    Route::get('/auth/google/callback', [QuizAuthController::class, 'handleGoogleCallback']);

    // Public data - Exams list
    Route::get('/exams', [ExamController::class, 'index']);

    // Public - Theory
    Route::get('/topics', [TheoryController::class, 'topics']);
    Route::get('/theories', [TheoryController::class, 'all']);
    Route::get('/theories/{slug}', [TheoryController::class, 'show']);
    Route::get('/topics/{slug}', [TheoryController::class, 'topic']);

    // Leaderboard (public)
    Route::get('/leaderboard', [StatisticsController::class, 'leaderboard']);
});

// Protected Quiz routes (require auth)
Route::prefix('quiz')->middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [QuizAuthController::class, 'logout']);
    Route::get('/me', [QuizAuthController::class, 'me']);

    // Exam
    Route::get('/exams/{slug}', [ExamController::class, 'show']);
    Route::post('/submit', [ExamController::class, 'submit']);
    Route::get('/history', [ExamController::class, 'history']);
    Route::get('/attempts/{id}', [ExamController::class, 'attemptDetail']);

    // Statistics
    Route::get('/statistics/overview', [StatisticsController::class, 'overview']);
    Route::get('/statistics/progress', [StatisticsController::class, 'progress']);
});

// =====================================================
// CINEMA API ROUTES (Existing)
// =====================================================

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register']);
// Webhook không cần authAI
Route::post('/payment/webhook', [OrderController::class, 'webhook']);

Route::middleware('auth:sanctum')->group(function () {
     Route::post('/logout', [AuthController::class, 'logout']);
     Route::get('/info', [AuthController::class, 'getInfo']);


     Route::get('/movies', [MovieController::class, 'index']);
     Route::get('movies/{id}/showtimes', [MovieController::class, 'getShowtimes']);
     Route::get('/screens', [ScreenController::class, 'index']);
     Route::get('/showtimes/{showtime}/seat-map', [ShowtimeController::class, 'getSeatMap']);

     Route::put('/accounts/{account}', [AccountController::class, 'update']);

     Route::get('/screens/{screen}', [ScreenController::class, 'show']);
     Route::get('/products', [ProductController::class, 'index']);
     Route::get('/products/{product}', [ProductController::class, 'show']);
     Route::get('/seats', [SeatController::class, 'index']);
     Route::get('/seats/{seat}', [SeatController::class, 'show']);
     Route::get('/showtimes', [ShowtimeController::class, 'index']);
     Route::get('/showtimes/{showtime}', [ShowtimeController::class, 'show']);

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
          Route::post('/screens/{screen}/generate', [SeatController::class, 'generateSeats']);
          Route::apiResource('/showtimes', ShowtimeController::class)
               ->except(['index', 'show']);
          Route::post('/showtimes/generate', [ShowtimeController::class, 'generateShowtimes']);
          Route::post('/movies/generateSchedule', [MovieController::class, 'generateSchedule']);
     });
});
