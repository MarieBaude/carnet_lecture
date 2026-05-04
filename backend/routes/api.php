<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\AuthorController;
use App\Http\Controllers\Api\V1\BookController;
use App\Http\Controllers\Api\V1\GenreController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Auth
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Catalogue (public)
    Route::get('/books', [BookController::class, 'index']);
    Route::get('/books/{id}', [BookController::class, 'show']);
    Route::get('/genres', [GenreController::class, 'index']);
    Route::get('/authors', [AuthorController::class, 'index']);

    // Protégé
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
    });
});