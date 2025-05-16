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
use App\Http\Controllers\StageController;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| This file defines API routes for version 1 (v1). All routes are
| assigned to the "api" middleware group.
|
*/

Route::prefix('v1')->group(function () {
    // Public Auth Routes
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('password/forgot', [AuthController::class, 'forgotPassword']);
    Route::post('login/judge', [AuthController::class, 'judgeLogin']);
    Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle']);
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

    // Authenticated User Routes
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('users', [UserController::class, 'index']);
        Route::get('user', [UserController::class, 'show']);
        Route::post('users', [UserController::class, 'store']);
        Route::patch('users/{user_id}', [UserController::class, 'update']);
        Route::delete('users/{user_id}', [UserController::class, 'destroy']);
    });

    // Admin and Tabulator-only routes
    Route::middleware(['auth:sanctum', 'role:admin,tabulator'])->group(function () {
        // Events
        Route::prefix('events')->group(function () {
            Route::post('create', [EventController::class, 'store']);
            Route::get('/', [EventController::class, 'index']);
            Route::get('{event_id}', [EventController::class, 'show']);
            Route::patch('{event_id}/edit', [EventController::class, 'update']);
            Route::delete('{event_id}', [EventController::class, 'destroy']);
            Route::post('{event_id}/start', [EventController::class, 'start']);
            Route::post('{event_id}/finalize', [EventController::class, 'finalize']);
            Route::post('{event_id}/reset', [EventController::class, 'reset']);

            Route::prefix('{event_id}')->group(function () {
                Route::prefix('stages')->group(function () {
                    Route::get('/', [StageController::class, 'index']);
                    Route::post('/create', [StageController::class, 'store']);
                    Route::get('/{stage_id}', [StageController::class, 'show']);
                    Route::patch('/{stage_id}/edit', [StageController::class, 'update']);
                    Route::delete('/{stage_id}', [StageController::class, 'destroy']);
                    Route::post('/{stage_id}/start', [StageController::class, 'start']);
                    Route::post('/{stage_id}/finalize', [StageController::class, 'finalize']);
                    Route::post('/{stage_id}/reset', [StageController::class, 'reset']);
                    Route::post('/{stage_id}/select-top-candidates', [StageController::class, 'selectTopCandidates']);
                    Route::post('/{stage_id}/reset-top-candidates', [StageController::class, 'resetTopCandidates']);
                    Route::get('/{stage_id}/partial-results', [StageController::class, 'partialResults']);
                });

                Route::prefix('categories')->group(function () {
                    Route::get('pending-scores', [CategoryController::class, 'hasPendingScoresForAll']);
                    Route::get('/', [CategoryController::class, 'index']);
                    Route::post('create', [CategoryController::class, 'store']);
                    Route::get('{category_id}', [CategoryController::class, 'show']);
                    Route::patch('{category_id}/edit', [CategoryController::class, 'update']);
                    Route::delete('{category_id}', [CategoryController::class, 'destroy']);
                    Route::post('{category_id}/start', [CategoryController::class, 'start']);
                    Route::post('{category_id}/finalize', [CategoryController::class, 'finalize']);
                    Route::post('{category_id}/reset', [CategoryController::class, 'reset']);
                    Route::post('{category_id}/set-candidate', [CategoryController::class, 'setCandidate']);
                    Route::get('{category_id}/pending-scores', [CategoryController::class, 'hasPendingScores']);
                });

                Route::prefix('candidates')->group(function () {
                    Route::get('/', [CandidateController::class, 'index']);
                    Route::post('create', [CandidateController::class, 'store']);
                    Route::get('{candidate_id}', [CandidateController::class, 'show']);
                    Route::patch('{candidate_id}/edit', [CandidateController::class, 'update']);
                    Route::delete('{candidate_id}', [CandidateController::class, 'destroy']);
                });

                Route::prefix('judges')->group(function () {
                    Route::get('/', [JudgeController::class, 'index']);
                    Route::post('create', [JudgeController::class, 'store']);
                    Route::get('{judge_id}', [JudgeController::class, 'show']);
                    Route::patch('{judge_id}/edit', [JudgeController::class, 'update']);
                    Route::delete('{judge_id}', [JudgeController::class, 'destroy']);
                });

                Route::prefix('scores')->group(function () {
                    Route::get('/', [ScoreController::class, 'index']);
                    Route::post('create', [ScoreController::class, 'store']);
                    Route::get('show', [ScoreController::class, 'show']);
                    Route::patch('edit/{judge_id}/{candidate_id}/{category_id}', [ScoreController::class, 'update']);
                    Route::delete('delete', [ScoreController::class, 'destroy']);
                });

                Route::get('report', [PdfReportController::class, 'download']);
                Route::get('results/preview', [PdfReportController::class, 'preview']);
            });
        });
    });

    // Judge-only routes
    Route::middleware(['auth:sanctum', 'role:judge', 'no.cache'])->group(function () {
        Route::prefix('judge')->group(function () {
            Route::get('current-session', [JudgeController::class, 'currentSession']);
            Route::get('scoring-session', [JudgeController::class, 'scoringSession']);
            Route::post('submit-score', [ScoreController::class, 'submit']);
            Route::post('confirm-score', [ScoreController::class, 'confirm']);
            Route::get('final-results', [ScoreController::class, 'finalResults']);
        });
    });
});