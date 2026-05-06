<?php

use App\Http\Controllers\Api\V1\ActivityController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\AuthorController;
use App\Http\Controllers\Api\V1\BookController;
use App\Http\Controllers\Api\V1\GenreController;
use App\Http\Controllers\Api\V1\LibraryController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\HomeController;
use App\Http\Controllers\Api\V1\UploadController;
use App\Http\Controllers\Api\V1\SagaController;

Route::prefix('v1')->group(function () {
    // Auth
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Catalogue (public)
    Route::get('/books', [BookController::class, 'index']);
    Route::get('/books/{id}', [BookController::class, 'show']);
    Route::get('/genres', [GenreController::class, 'index']);
    Route::get('/authors', [AuthorController::class, 'index']);

    // Profil public
    Route::get('/users/{id}', [UserController::class, 'show']);

    // Stats et commentaires (public)
    Route::get('/books/{id}/stats', [BookController::class, 'stats']);
    Route::get('/books/{id}/comments', [BookController::class, 'comments']);

    Route::get('/users/{id}/activity', [ActivityController::class, 'user']);
    Route::get('/sagas', [SagaController::class, 'index']);
    Route::get('/sagas/{id}', [SagaController::class, 'show']);

    // Protégé
    Route::middleware('auth:sanctum')->group(function () {
        // Auth
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);

        // Bibliothèque
        Route::prefix('library')->group(function () {
            Route::get('/books', [LibraryController::class, 'index']);
            Route::post('/books', [LibraryController::class, 'store']);
            Route::patch('/books/{bookId}', [LibraryController::class, 'update']);
            Route::delete('/books/{bookId}', [LibraryController::class, 'destroy']);
            Route::get('/stats', [LibraryController::class, 'stats']);
            Route::get('/shelves', [LibraryController::class, 'shelves']);
            Route::patch('/books/{bookId}/feature', [LibraryController::class, 'feature']);
            Route::patch('/books/{bookId}/finish', [LibraryController::class, 'finish']);
        });

        // Profil connecté
        Route::get('/me', [UserController::class, 'me']);
        Route::patch('/me', [UserController::class, 'updateMe']);
        Route::get('/me/followers', [UserController::class, 'followers']);
        Route::get('/me/following', [UserController::class, 'following']);
        Route::get('/me/activity', [ActivityController::class, 'me']);

        // Social
        Route::post('/users/{id}/follow', [UserController::class, 'follow']);
        Route::delete('/users/{id}/follow', [UserController::class, 'unfollow']);

        // Notation et commentaires
        Route::post('/books/{id}/rate', [BookController::class, 'rate']);
        Route::post('/books/{id}/comments', [BookController::class, 'storeComment']);

        Route::get('/home', [HomeController::class, 'index']);
        Route::post('/upload', [UploadController::class, 'store']);

        // Livres
        Route::post('/books', [BookController::class, 'store']);
        Route::patch('/books/{id}', [BookController::class, 'update']);
        Route::delete('/books/{id}', [BookController::class, 'destroy']);

        // Sagas
        Route::post('/sagas', [SagaController::class, 'store']);
        Route::patch('/sagas/{id}', [SagaController::class, 'update']);
        Route::delete('/sagas/{id}', [SagaController::class, 'destroy']);
        Route::post('/sagas/{id}/books', [SagaController::class, 'addBook']);
        Route::patch('/sagas/{id}/books/{bookId}', [SagaController::class, 'updateBook']);
        Route::delete('/sagas/{id}/books/{bookId}', [SagaController::class, 'removeBook']);

    });
});