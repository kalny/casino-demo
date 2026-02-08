<?php

use App\Http\Controllers\Api\Game\GameController;
use App\Http\Controllers\Api\Game\PlayGameController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::prefix('games')->name('games.')->group(function () {
        Route::get('/', [GameController::class, 'index'])->name('index');
        Route::get('/{id}', [GameController::class, 'show'])->name('show');
        Route::post('/{id}/play', [PlayGameController::class, 'play'])->name('play');
    });
});
