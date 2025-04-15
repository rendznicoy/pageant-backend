<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\JudgeController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\PdfReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
// Public Auth Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);

    // Users
    Route::apiResource('users', UserController::class);

    // Events
    Route::apiResource('events', EventController::class);
    Route::post('/events/{id}/start', [EventController::class, 'start']);
    Route::post('/events/{id}/finalize', [EventController::class, 'finalize']);
    Route::post('/events/{id}/reset', [EventController::class, 'reset']);

    // Categories
    Route::apiResource('categories', CategoryController::class);

    // Candidates
    Route::apiResource('candidates', CandidateController::class);

    // Judges
    Route::apiResource('judges', JudgeController::class);

    // Scores
    Route::apiResource('scores', ScoreController::class)->only(['index', 'store', 'show']);

    // PDF Download
    Route::get('/pdf/event-scores/{event_id}', [PdfReportController::class, 'download']);
});