<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\AuthorController;
use App\Http\Controllers\Api\V1\BookController;
use App\Http\Controllers\Api\V1\GenreController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\LibraryController;

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

    // Bibliothèque (protégé)
    Route::prefix('library')->group(function () {
        Route::get('/books', [LibraryController::class, 'index']);
        Route::post('/books', [LibraryController::class, 'store']);
        Route::patch('/books/{bookId}', [LibraryController::class, 'update']);
        Route::delete('/books/{bookId}', [LibraryController::class, 'destroy']);
        Route::get('/stats', [LibraryController::class, 'stats']);
        Route::get('/shelves', [LibraryController::class, 'shelves']);
    });
});