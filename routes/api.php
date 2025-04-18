<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ApiAuthMiddleware;
use Illuminate\Support\Facades\Route;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/hello', function() {
    return 'hello mother fucker!';
});

// route group use middleware auth
Route::middleware(ApiAuthMiddleware::class)->group(function() {
    Route::get('/user/current', [UserController::class, 'getUser']);
    Route::put('/user/current', [UserController::class, 'update']);
    Route::post('/user/logout', [UserController::class, 'logout']);

    Route::post('/contact', [ContactController::class, 'create']);
});