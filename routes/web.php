<?php
// routes/web.php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Sanctum CSRF cookie route - this is the standard Sanctum route
Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
});

// You can also add a custom route if needed
Route::get('/api/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
});

// Your other web routes...
Route::get('/', function () {
    return response()->json(['message' => 'API is running']);
});