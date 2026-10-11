<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\MahasiswaController;
use App\Http\Controllers\API\BeasiswaController;
use App\Http\Controllers\API\SuratController;
use App\Http\Controllers\API\ChatController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\ConsultationController;

Route::prefix('v1')->group(function () {

    // PUBLIC AUTH
    Route::post('/login', [AuthController::class, 'login']);

    // PROTECTED ROUTES (Requires Sanctum Token)
    Route::middleware('auth:sanctum')->group(function () {

        // User Auth
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        // Admin dashboard test routes
        Route::middleware('role:admin')->group(function () {
            Route::get('/admin/dashboard', fn () => 'Admin Area');
            Route::get('/test-admin', fn () => 'ADMIN ACCESS GRANTED');
        });

        // Mahasiswa List
        Route::get('/mahasiswa', [MahasiswaController::class, 'index']);

        // Beasiswa CRUD
        Route::get('/beasiswa', [BeasiswaController::class, 'index']);
        Route::get('/beasiswa/{id}', [BeasiswaController::class, 'show']);
        
        Route::middleware('role:admin')->group(function () {
            Route::post('/beasiswa', [BeasiswaController::class, 'store']);
            Route::put('/beasiswa/{id}', [BeasiswaController::class, 'update']);
            Route::delete('/beasiswa/{id}', [BeasiswaController::class, 'destroy']);
        });

        // Surat Module
        Route::post('/surat', [SuratController::class, 'store']);
        Route::get('/surat', [SuratController::class, 'index']);
        Route::put('/surat/{id}/status', [SuratController::class, 'updateStatus'])
             ->middleware('role:staff'); // Or role:admin/wd3 via custom checks in controller or multiple middlewares

        // Consultation Module
        Route::post('/consultation', [ConsultationController::class, 'store']);
        Route::get('/consultation', [ConsultationController::class, 'index']);
        Route::put('/consultation/{id}/status', [ConsultationController::class, 'updateStatus']);

        // Live Chat Module
        Route::get('/chat', [ChatController::class, 'index']);
        Route::post('/chat', [ChatController::class, 'startChat']);
        Route::post('/chat/{id}/message', [ChatController::class, 'sendMessage']);

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index']);
    });
});