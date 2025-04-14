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

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/user', fn(Request $request) => $request->user());
Route::apiResource('users', UserController::class)->only(['index', 'store']);
Route::apiResource('events', EventController::class);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('candidates', CandidateController::class);
Route::apiResource('judges', JudgeController::class);
Route::get('/scores', [ScoreController::class, 'index']);
Route::post('/scores', [ScoreController::class, 'store']);