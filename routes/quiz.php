<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Quiz\AuthController;
use App\Http\Controllers\Quiz\ExamController;
use App\Http\Controllers\Quiz\StatisticsController;
use App\Http\Controllers\Quiz\TheoryController;

/*
|--------------------------------------------------------------------------
| Quiz Web Routes
|--------------------------------------------------------------------------
*/

// Login page
Route::get('/login', [AuthController::class, 'showLogin'])->name('quiz.login');

// Google OAuth
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('quiz.auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('quiz.auth.google.callback');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('quiz.logout')->middleware('auth:quiz');

// Protected routes - require login
Route::middleware('auth:quiz')->group(function () {
    // Home - React SPA
    Route::get('/', function () {
        return view('quiz.app');
    })->name('quiz.home');

    // Catch-all for SPA routing
    Route::get('/{any}', function () {
        return view('quiz.app');
    })->where('any', '^(?!api).*$');
});

/*
|--------------------------------------------------------------------------
| Quiz API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('api')->middleware('auth:quiz')->group(function () {
    // User info
    Route::get('/me', [AuthController::class, 'me']);

    // Exams
    Route::get('/exams', [ExamController::class, 'index']);
    Route::get('/exams/{slug}', [ExamController::class, 'show']);
    Route::post('/exams/{slug}/start', [ExamController::class, 'start']);
    Route::post('/exams/{slug}/submit', [ExamController::class, 'submit']);
    Route::get('/attempts/{id}/result', [ExamController::class, 'result']);

    // Statistics
    Route::get('/statistics/overview', [StatisticsController::class, 'overview']);
    Route::get('/statistics/history', [StatisticsController::class, 'history']);
    Route::get('/statistics/progress', [StatisticsController::class, 'progress']);
    Route::get('/statistics/leaderboard', [StatisticsController::class, 'leaderboard']);

    // Theory
    Route::get('/topics', [TheoryController::class, 'topics']);
    Route::get('/topics/{slug}', [TheoryController::class, 'topic']);
    Route::get('/theories', [TheoryController::class, 'all']);
    Route::get('/theories/{slug}', [TheoryController::class, 'show']);
});
