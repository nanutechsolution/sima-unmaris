<?php

use App\Http\Controllers\Api\SurveyApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


/**
 * 1. RUTE TES KONEKSI (GET)
 * Akses: https://siaset.unmarissumba.ac.id/api/v1/test-api
 * Gunakan ini di browser untuk memastikan URL api/v1 terbaca oleh Nginx.
 */
Route::get('/v1/test-api', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Koneksi ke API Siaset Berhasil!',
        'timestamp' => now()
    ]);
});
/**
 * API ROUTES - SIMA UNMARIS
 */

Route::post('/v1/login', [SurveyApiController::class, 'login']);
Route::post('/v1/login-siakad', [SurveyApiController::class, 'login']);
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {

    // RUTE BARU: Mengambil semua daftar survei untuk dashboard
    Route::get('/surveys', [SurveyApiController::class, 'getAvailableSurveys']);

    Route::get('/surveys/{id}', [SurveyApiController::class, 'getSurveyDetails']);
    Route::post('/surveys/{id}/submit', [SurveyApiController::class, 'submitResponse']);
});
