<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ConcentrateController;
use App\Http\Controllers\RequirementController;

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
});

Route::group(['middleware' => ['role:admin|akademik']], function () {
    Route::prefix('users')->controller(UserController::class)->name('users.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/data', 'data')->name('data');
        Route::post('/', 'store')->name('store');
        Route::get('/{user}/edit', 'edit')->name('edit');
        Route::put('/{user}', 'update')->name('update');
        Route::delete('/{user}', 'destroy')->name('destroy');
    });

    Route::prefix('prodi')->controller(MajorController::class)->name('prodi.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/data', 'data')->name('data');
        Route::post('/', 'store')->name('store');
        Route::get('/{major}/edit', 'edit')->name('edit');
        Route::put('/{major}', 'update')->name('update');
        Route::delete('/{major}', 'destroy')->name('destroy');
    });

    Route::prefix('concentrates')->controller(ConcentrateController::class)->name('concentrates.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/data', 'data')->name('data');
        Route::post('/', 'store')->name('store');
        Route::get('/{concentrate}/edit', 'edit')->name('edit');
        Route::put('/{concentrate}', 'update')->name('update');
        Route::delete('/{concentrate}', 'destroy')->name('destroy');
    });

    Route::prefix('requirements')->controller(RequirementController::class)->name('requirements.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/data', 'data')->name('data');
        Route::post('/', 'store')->name('store');
        Route::get('/{requirement}/edit', 'edit')->name('edit');
        Route::put('/{requirement}', 'update')->name('update');
        Route::delete('/{requirement}', 'destroy')->name('destroy');
    });
});
