<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\KaprodiController;
use App\Http\Controllers\AkademikController;
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
    Route::get('/', fn () => redirect()->route('dashboard'))->name('home');
    Route::get('/dashboard', fn () => view('welcome'))->name('dashboard');

    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
});

Route::name('submissions.')->middleware(['web','auth'])->group(function () {
    Route::get('/submissions', [SubmissionController::class, 'index'])->name('create');

    // form dinamis
    Route::get('/submissions/types/{letterTypeId}/form', [SubmissionController::class, 'form'])->name('type.form');

    // store pengajuan
    Route::post('/', [SubmissionController::class, 'store'])->name('store');

    // receipt
    Route::get('/submissions/{submission}/receipt', [SubmissionController::class, 'receipt'])->name('receipt');

    // download receipt as PDF
    Route::get('/submissions/{submission}/receipt/download', [SubmissionController::class, 'downloadReceipt'])->name('receipt.download');

    // print receipt
    Route::get('/submissions/{submission}/receipt/print', [SubmissionController::class, 'printReceipt'])->name('receipt.print');

    Route::get('/my-submissions', [SubmissionController::class, 'mySubmissions'])->name('index');
    Route::get('/my-submissions/datatable', [SubmissionController::class, 'mySubmissionsDatatable'])->name('index.datatable');

    Route::get('/submissions/{submission}/statuses/data', [SubmissionController::class, 'submissionStatusesDatatable'])->name('statuses.data');

    //chat routes
    Route::get('/submissions/{submission}/chat', [SubmissionController::class, 'chatIndex'])->name('chat.index');
    Route::post('/submissions/{submission}/chat', [SubmissionController::class, 'chatStore'])->name('chat.store');

    // edit submission rute
    Route::get('/submissions/{submission}/edit-modal', [SubmissionController::class, 'editFormModal'])->name('submissions.editModal');
    Route::put('/submissions/{submission}', [SubmissionController::class, 'update'])->name('update');
});

Route::group(['middleware' => ['role:kaprodi']], function() {
    Route::prefix('kaprodi')->controller(KaprodiController::class)->name('kaprodi.')->group(function () {
        Route::get('/submissions/{type}/{status}', 'index')->name('submissions.index')
            ->where('type', 'surat|ujian')
            ->where('status', 'proses|selesai');
        Route::get('/submissions/{type}/{status}/datatable', 'indexDatatable')->name('submissions.index.datatable')
            ->where('type', 'surat|ujian')
            ->where('status', 'proses|selesai');

        Route::get('/submissions/{submission}/attachments', 'getAttachments')->name('submissions.attachments');
        Route::get('/submissions/{submission}/preview', 'previewSubmission')->name('submissions.preview');
        Route::get('/submissions/{submission}/preview/html', 'getLetterPreviewHtml')->name('submissions.preview.html');
        Route::put('/submissions/{submission}/process', 'processSubmission')->name('submissions.process');

        Route::get('/submissions/{submission}/edit-modal', 'editFormModal')->name('submissions.edit.modal');
        Route::put('/submissions/{submission}/update-revision', 'update')->name('submissions.update.revision');
    });
});

Route::group(['middleware' => ['role:akademik']], function() {
    Route::prefix('akademik')->controller(AkademikController::class)->name('akademik.')->group(function () {
        // Route baru dengan parameter type dan status
        Route::get('/submissions/{type}/{status}', 'index')->name('submissions.index')
            ->where('type', 'surat|ujian')
            ->where('status', 'proses|selesai');
        Route::get('/submissions/{type}/{status}/datatable', 'indexDatatable')->name('submissions.index.datatable')
            ->where('type', 'surat|ujian')
            ->where('status', 'proses|selesai');

        Route::get('/submissions/{submission}/attachments', 'getAttachments')->name('submissions.attachments');
        Route::get('/submissions/{submission}/preview', 'previewSubmission')->name('submissions.preview');
        Route::get('/submissions/{submission}/preview/html', 'getLetterPreviewHtml')->name('submissions.preview.html');
        Route::put('/submissions/{submission}/process', 'processSubmission')->name('submissions.process');

        Route::get('/submissions/{submission}/edit', 'editSubmission')->name('submissions.edit');
        Route::put('/submissions/{submission}/update', 'updateSubmission')->name('submissions.update');

        Route::get('/submissions/{submission}/print', 'printLetter')->name('submissions.print');
        Route::get('/submissions/{submission}/download', 'downloadLetter')->name('submissions.download');

        Route::post('/submissions/{submission}/upload-final', 'uploadFinalLetter')->name('submissions.uploadFinal');
    });

    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])
      ->name('users.resetPassword');

    Route::post('/users/import', [UserController::class, 'import'])->name('users.import');
    Route::get('/users/import-template', [UserController::class, 'downloadTemplate'])->name('users.downloadTemplate');

    Route::post('/users/import-mahasiswa', [UserController::class, 'importMahasiswa'])->name('users.importMahasiswa');
    Route::get('/users/import-mahasiswa-template', [UserController::class, 'downloadMahasiswaTemplate'])->name('users.downloadMahasiswaTemplate');

    Route::post('/users/import-dosen', [UserController::class, 'importDosen'])->name('users.importDosen');
    Route::get('/users/import-dosen-template', [UserController::class, 'downloadDosenTemplate'])->name('users.downloadDosenTemplate');
});

Route::group(['middleware' => ['role:akademik']], function () {
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

        // Route baru untuk template editing dengan iframe approach
        Route::get('/{letter_type}/edit-template', 'editTemplate')->name('edit-template');
        Route::post('/{letter_type}/preview-template', 'previewTemplateHtml')->name('preview-template');
        Route::put('/{letter_type}/update-template', 'updateTemplate')->name('update-template');
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
