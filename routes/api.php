<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::middleware('abilities:books:read')->group(function () {
        Route::get('/books', [BookController::class, 'index']);
        Route::get('/books/{book}', [BookController::class, 'show'])->whereNumber('book');
    });

    Route::middleware('abilities:books:write')->group(function () {
        Route::post('/books', [BookController::class, 'store']);
        Route::match(['put', 'patch'], '/books/{book}', [BookController::class, 'update'])->whereNumber('book');
        Route::delete('/books/{book}', [BookController::class, 'destroy'])->whereNumber('book');
    });
});