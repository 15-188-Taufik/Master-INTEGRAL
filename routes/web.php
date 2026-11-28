<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| MODUL 1: Identity, Data Master & Manajemen Siklus Pelatihan
|--------------------------------------------------------------------------
*/

// 1. PUBLIC ROUTES (Tanpa Login)
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/search', [SearchController::class, 'index'])->name('global.search');

// Otentikasi Google OAuth untuk Peserta
Route::get('auth/google', [LoginController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [LoginController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// 2. AUTHENTICATION SYSTEM
Auth::routes(['register' => false]);

Route::get('/logout', function() {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/')->with('success', 'Anda telah berhasil keluar.');
})->name('logout');

// 3. AUTHENTICATED SYSTEM (Role-Based Access Control)
Route::middleware(['auth'])->group(function () {

    // --- PENGATURAN SISTEM (Khusus Superadmin) ---
    Route::middleware(['can:superadmin-only'])->group(function () {
        // CRUD Admin Bidang
        Route::resource('users', UserController::class);
        Route::put('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

        // Audit Trail / Log Aktivitas Admin (IP Address & Browser)
        Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    });

    // --- DASHBOARD & MANAJEMEN PELATIHAN (Khusus Superadmin & Admin Bidang) ---
    Route::middleware(['can:admin-only'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Manajemen Pelatihan
        Route::resource('trainings', TrainingController::class);
        Route::get('trainings/{id}/manage', [TrainingController::class, 'manage'])->name('trainings.manage');
        Route::get('trainings/{id}/new-code', [TrainingController::class, 'generateNewCode'])->name('trainings.new_code');
        Route::put('trainings/{id}/set-lms', [TrainingController::class, 'setLmsLink'])->name('trainings.set_lms');

        // Manajemen Peserta (Single Input Data & Smart Linking)
        Route::get('trainings/{id}/participants', [TrainingController::class, 'showParticipants'])->name('trainings.participants');
        Route::post('trainings/{id}/participants/manual', [TrainingController::class, 'storeParticipant'])->name('participants.store');
        Route::post('trainings/{id}/participants/import', [TrainingController::class, 'importParticipants'])->name('participants.import');
        Route::put('participants/{id}', [TrainingController::class, 'updateParticipant'])->name('participants.update');
        Route::delete('participants/{id}', [TrainingController::class, 'destroyParticipant'])->name('participants.destroy');
        Route::get('participants/download-template', [TrainingController::class, 'downloadTemplate'])->name('participants.template');
        Route::get('trainings/{id}/export-participants-data', [TrainingController::class, 'exportParticipants'])->name('participants.export_data');

        // MODUL 4 (Iterasi 1 - F-36 / US-36): Hierarki Berkas Bersarang & Folder Global
        Route::get('documents', [DocumentController::class, 'index'])->name('documents.index');
        Route::post('documents/folder', [DocumentController::class, 'createFolder'])->name('documents.folder.create');
        Route::delete('documents/folder/{folder}', [DocumentController::class, 'destroyFolder'])->name('documents.folder.destroy');
        Route::post('documents/upload', [DocumentController::class, 'uploadFile'])->name('documents.upload');
        Route::get('documents/file/{file}/view', [DocumentController::class, 'viewFile'])->name('documents.file.view');
        Route::get('documents/file/{file}/download', [DocumentController::class, 'downloadFile'])->name('documents.file.download');
        Route::delete('documents/file/{file}', [DocumentController::class, 'destroyFile'])->name('documents.file.destroy');
    });

    // --- PROFIL PENGGUNA ---
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // --- PELENGKAPAN PROFIL AWAL PESERTA (Pohon Wilayah 4 Tingkat) ---
    Route::get('/complete-profile', [ParticipantController::class, 'completeProfile'])->name('participant.profile.complete');
    Route::post('/complete-profile', [ParticipantController::class, 'storeProfile'])->name('participant.profile.store');

    // --- PORTAL PESERTA (Khusus Role Participant) ---
    Route::middleware(['can:isParticipant'])->prefix('participant')->group(function () {
        Route::get('/dashboard', [ParticipantController::class, 'index'])->name('participant.dashboard');
        Route::get('/trainings', [ParticipantController::class, 'availableTrainings'])->name('participant.trainings');
        Route::post('/training/{id}/join', [ParticipantController::class, 'enroll'])->name('participant.training.join');
        Route::get('/training/{id}/show', [ParticipantController::class, 'showTrainingDetail'])->name('participant.training.show');
        Route::get('/history', [ParticipantController::class, 'myHistory'])->name('participant.history');
    });

});