<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MajorController;
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
    Route::get('/create-submission', [SubmissionController::class, 'index'])->name('submission.create');

    // Ambil HTML field dinamis (untuk inject ke modal)
    Route::get('/submissions/types/{letterTypeId}/form', [SubmissionController::class, 'form'])->name('submissions.type.form');

    // Submit pengajuan
    Route::post('/submissions', [SubmissionController::class, 'store'])->name('submissions.store');
});

Route::group(['middleware' => ['role:admin']], function () {
    Route::prefix('users')->controller(UserController::class)->name('users.')->group(function () {
        // Halaman utama, URI akan menjadi 'users/'
        Route::get('/', 'index')->name('index');

        // Sumber data untuk DataTables
        Route::get('/data', 'data')->name('data');

        // Menyimpan data baru, URI akan menjadi 'users/' (POST)
        Route::post('/', 'store')->name('store');

        // Mengambil data untuk diedit, URI akan menjadi 'users/{id}/edit'
        Route::get('/{user}/edit', 'edit')->name('edit');

        // Mengupdate data, URI akan menjadi 'users/{id}' (PUT)
        Route::put('/{user}', 'update')->name('update');

        // Menghapus data, URI akan menjadi 'users/{id}' (DELETE)
        Route::delete('/{user}', 'destroy')->name('destroy');
    });

    Route::prefix('prodi')->controller(MajorController::class)->name('prodi.')->group(function () {
        // Halaman utama, URI akan menjadi 'prodi/'
        Route::get('/', 'index')->name('index');

        // Sumber data untuk DataTables
        Route::get('/data', 'data')->name('data');

        // Menyimpan data baru, URI akan menjadi 'users/' (POST)
        Route::post('/', 'store')->name('store');

        // Mengambil data untuk diedit, URI akan menjadi 'users/{id}/edit'
        Route::get('/{major}/edit', 'edit')->name('edit');

        // Mengupdate data, URI akan menjadi 'users/{id}' (PUT)
        Route::put('/{major}', 'update')->name('update');

        // Menghapus data, URI akan menjadi 'users/{id}' (DELETE)
        Route::delete('/{major}', 'destroy')->name('destroy');
    });
});
