<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\BookingController;

// Redirect root to login if not authenticated, otherwise to movies
Route::get('/', function () {
    if (auth()->guard('web')->check()) {
        return redirect()->route('booking.index');
    }
    return redirect()->route('auth.login.form');
})->name('home');

// Booking Routes (Requires Authentication)
Route::middleware('auth:web')->group(function () {
    Route::get('/movies', [BookingController::class, 'index'])->name('booking.index');
    Route::get('/movies/{id}', [BookingController::class, 'showMovieDetail'])->name('booking.movie.detail');
    Route::get('/showtimes/{id}/seats', [BookingController::class, 'seatMap'])->name('booking.seat-map');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Authentication Routes (Guest only)
Route::middleware('guest:web')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('auth.login.form');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('auth.register.form');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
});

// Logout (Authenticated only)
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout')->middleware('auth:web');
