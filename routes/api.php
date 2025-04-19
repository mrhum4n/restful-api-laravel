<?php

use App\Http\Controllers\AddressController;
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
    Route::get('/contact/{id}', [ContactController::class, 'get'])->where('id', '[0-9]+');
    Route::put('/contact/{id}', [ContactController::class, 'update'])->where('id', '[0-9]+');
    Route::delete('/contact/{id}', [ContactController::class, 'detele'])->where('id', '[0-9]+');
    Route::get('/contacts', [ContactController::class, 'search']);

    Route::post('/contact/{id_contact}/address', [AddressController::class, 'create'])->where('id_contact', '[0-9]+');
    Route::get('/contact/{id_contact}/address/{id_address}', [AddressController::class, 'get'])
        ->where('id_contact', '[0-9]+')
        ->where('id_address', '[0-9]+');
    Route::put('/contact/{id_contact}/address/{id_address}', [AddressController::class, 'update'])
        ->where('id_contact', '[0-9]+')
        ->where('id_address', '[0-9]+');
    Route::delete('/contact/{id_contact}/address/{id_address}', [AddressController::class, 'delete'])
        ->where('id_contact', '[0-9]+')
        ->where('id_address', '[0-9]+');
    Route::get('/contact/{id_contact}/addresses', [AddressController::class, 'getAll'])->where('id_contact', '[0-9]+');
});