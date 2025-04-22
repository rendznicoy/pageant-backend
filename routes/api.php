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

            // Event status actions
            Route::post('{event_id}/start', [EventController::class, 'start']);
            Route::post('{event_id}/finalize', [EventController::class, 'finalize']);
            Route::post('{event_id}/reset', [EventController::class, 'reset']);

            // Nested resources within an event
            Route::prefix('{event_id}')->group(function () {

                // Categories
                Route::prefix('categories')->group(function () {
                    Route::get('/', [CategoryController::class, 'index']);
                    Route::post('create', [CategoryController::class, 'store']);
                    Route::get('{category_id}', [CategoryController::class, 'show']);
                    Route::patch('{category_id}/edit', [CategoryController::class, 'update']);
                    Route::delete('{category_id}', [CategoryController::class, 'destroy']);
                });

                // Candidates
                Route::prefix('candidates')->group(function () {
                    Route::get('/', [CandidateController::class, 'index']);
                    Route::post('create', [CandidateController::class, 'store']);
                    Route::get('{candidate_id}', [CandidateController::class, 'show']);
                    Route::patch('{candidate_id}/edit', [CandidateController::class, 'update']);
                    Route::delete('{candidate_id}', [CandidateController::class, 'destroy']);
                });

                // Judges
                Route::prefix('judges')->group(function () {
                    Route::get('/', [JudgeController::class, 'index']);
                    Route::post('create', [JudgeController::class, 'store']);
                    Route::get('{judge_id}', [JudgeController::class, 'show']);
                    Route::patch('{judge_id}/edit', [JudgeController::class, 'update']);
                    Route::delete('{judge_id}', [JudgeController::class, 'destroy']);
                });

                // Scores
                Route::prefix('scores')->group(function () {
                    Route::get('/', [ScoreController::class, 'index']);
                    Route::post('create', [ScoreController::class, 'store']);
                    Route::get('show', [ScoreController::class, 'show']);
                    Route::patch('edit/{judge_id}/{candidate_id}/{category_id}', [ScoreController::class, 'update']);
                    Route::delete('delete', [ScoreController::class, 'destroy']);
                });

                // PDF Report
                Route::get('report', [PdfReportController::class, 'download']);
            });
        });
    });
});