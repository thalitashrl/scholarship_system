<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LandingController;

Route::get('/', [LandingController::class, 'index'])->name('landing');

// WEB AUTHENTICATION ROUTES
Route::get('/login', [WebAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

// PROTECTED WEB ADMIN ROUTES (Requires session auth & admin_staff access)
Route::middleware(['auth', 'admin_staff'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Beasiswa CRUD (Web Panel)
    Route::get('/beasiswa', [AdminController::class, 'beasiswaIndex'])->name('beasiswa.index');
    Route::post('/beasiswa', [AdminController::class, 'beasiswaStore'])->name('beasiswa.store');
    Route::put('/beasiswa/{id}', [AdminController::class, 'beasiswaUpdate'])->name('beasiswa.update');
    Route::delete('/beasiswa/{id}', [AdminController::class, 'beasiswaDestroy'])->name('beasiswa.destroy');

    // Surat Approval (Web Panel)
    Route::get('/surat', [AdminController::class, 'suratIndex'])->name('surat.index');
    Route::put('/surat/{id}/status', [AdminController::class, 'suratUpdateStatus'])->name('surat.updateStatus');
    Route::post('/surat/{id}/upload', [AdminController::class, 'suratUpload'])->name('surat.upload');

    // Master Data (Admin Only)
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', \App\Http\Controllers\AdminUserController::class);
        Route::resource('jenis-surat', \App\Http\Controllers\JenisSuratController::class);
        Route::resource('jenis-beasiswa', \App\Http\Controllers\JenisBeasiswaController::class);
    });

});

// PROTECTED WEB MAHASISWA ROUTES
Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\MahasiswaPortalController::class, 'dashboard'])->name('dashboard');
    
    // Surat
    Route::get('/surat', [\App\Http\Controllers\MahasiswaPortalController::class, 'suratIndex'])->name('surat.index');
    Route::post('/surat', [\App\Http\Controllers\MahasiswaPortalController::class, 'suratStore'])->name('surat.store');
});

// PROTECTED LIVE CHAT ROUTES (Semua Role terautentikasi)
Route::middleware('auth')->prefix('chat')->name('chat.')->group(function () {
    Route::get('/', [\App\Http\Controllers\ChatPortalController::class, 'index'])->name('index');
    Route::post('/start', [\App\Http\Controllers\ChatPortalController::class, 'startChat'])->name('start');
    Route::post('/{id}/send', [\App\Http\Controllers\ChatPortalController::class, 'sendMessage'])->name('send');
    Route::get('/{id}/messages', [\App\Http\Controllers\ChatPortalController::class, 'getMessages'])->name('messages');
});

// PROTECTED WEB CONSULTATION ROUTES (Semua Role terautentikasi)
Route::middleware('auth')->prefix('consultation')->name('consultation.')->group(function () {
    Route::get('/', [\App\Http\Controllers\ConsultationController::class, 'index'])->name('index');
    Route::post('/', [\App\Http\Controllers\ConsultationController::class, 'store'])->name('store');
    Route::put('/{id}/status', [\App\Http\Controllers\ConsultationController::class, 'updateStatus'])->name('updateStatus');
});

