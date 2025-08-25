<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SubmissionController;

Route::get('/', function () {return view('welcome');});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    // throttle:6,1 = maksimal 6 percobaan per menit
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt')->middleware('throttle:6,1');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', fn () => view('welcome'))->name('dashboard');

    // Submission Letter routes
    Route::get('/create-submission', [SubmissionController::class, 'create'])->name('submission.create');
});
