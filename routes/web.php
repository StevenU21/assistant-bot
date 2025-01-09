<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TranscriptionController;
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
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/transcriptions', [TranscriptionController::class, 'index'])->name('transcriptions.index');
    Route::get('/transcriptions/create', [TranscriptionController::class, 'create'])->name('transcriptions.create');
    Route::post('/transcriptions', [TranscriptionController::class, 'store'])->name('transcriptions.store');
    Route::get('/transcriptions/{transcription}/download', [TranscriptionController::class, 'download'])->name('transcriptions.download');
    Route::get('/transcriptions/show/{transcription}', [TranscriptionController::class, 'show'])->name('transcriptions.show');
    Route::delete('/transcriptions/{transcription}', [TranscriptionController::class, 'destroy'])->name('transcriptions.destroy');
});

require __DIR__ . '/auth.php';
