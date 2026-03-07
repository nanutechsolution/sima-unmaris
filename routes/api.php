<?php

use App\Http\Controllers\Api\SurveyApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/**
 * API ROUTES - SIMA UNMARIS
 */

Route::post('/v1/login-siakad', [SurveyApiController::class, 'login']);

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    
    // RUTE BARU: Mengambil semua daftar survei untuk dashboard
    Route::get('/surveys', [SurveyApiController::class, 'getAvailableSurveys']);
    
    Route::get('/surveys/{id}', [SurveyApiController::class, 'getSurveyDetails']);
    Route::post('/surveys/{id}/submit', [SurveyApiController::class, 'submitResponse']);

});