<?php
// routes/web.php

use Illuminate\Support\Facades\Route;

// Sanctum CSRF cookie route
Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
});

// Alternative CSRF route if needed
Route::get('/api/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
});

Route::get('/', function () {
    return response()->json(['message' => 'API is running']);
});