<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TranscriptionController;
use App\Http\Controllers\TranslationController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('transcriptions')->name('transcriptions.')->group(function () {
        Route::get('/', [TranscriptionController::class, 'index'])->name('index');
        Route::post('/', [TranscriptionController::class, 'store'])->name('store');
        Route::get('/{transcription}/download', [TranscriptionController::class, 'download'])->name('download');
        Route::get('/show/{transcription}', [TranscriptionController::class, 'show'])->name('show');
        Route::delete('/{transcription}', [TranscriptionController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('translations')->name('translations.')->group(function () {
        Route::get('/', [TranslationController::class, 'index'])->name('index');
        Route::post('/', [TranslationController::class, 'translate'])->name('translate');
    });
});

require __DIR__ . '/auth.php';
