<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\LetterTypeController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\ConcentrateController;
use App\Http\Controllers\LetterFieldController;

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
    Route::get('/create-submission', [SubmissionController::class, 'index'])->name('submission.create');

    // Ambil HTML field dinamis (untuk inject ke modal)
    Route::get('/submissions/types/{letterTypeId}/form', [SubmissionController::class, 'form'])->name('submissions.type.form');

    // Submit pengajuan
    Route::post('/submissions', [SubmissionController::class, 'store'])->name('submissions.store');
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

    Route::prefix('letter-types')->controller(LetterTypeController::class)->name('letter-types.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/data', 'data')->name('data');
        Route::post('/', 'store')->name('store');
        Route::get('/{letter_type}/edit', 'edit')->name('edit');
        Route::put('/{letter_type}', 'update')->name('update');
        Route::delete('/{letter_type}', 'destroy')->name('destroy');
    });

    Route::prefix('letter-fields')->controller(LetterFieldController::class)->name('letter-fields.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/data', 'data')->name('data');
        Route::post('/', 'store')->name('store');
        Route::get('/{letter_field}/edit', 'edit')->name('edit');
        Route::put('/{letter_field}', 'update')->name('update');
        Route::delete('/{letter_field}', 'destroy')->name('destroy');
    });
});
