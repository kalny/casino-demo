<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Game\GameController;
use App\Http\Controllers\Api\Game\PlayGameController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])
            ->name('register');
        Route::post('/login', [AuthController::class, 'login'])
            ->name('login');
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout'])
                ->name('logout');
        });
    });

    Route::prefix('games')->name('games.')->group(function () {
        Route::get('/', [GameController::class, 'index'])->name('index');
        Route::get('/{id}', [GameController::class, 'show'])->name('show');
        Route::post('/{id}/play', [PlayGameController::class, 'play'])
            ->name('play')
            ->middleware('auth:sanctum');
    });
});
