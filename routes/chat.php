<?php

use Illuminate\Support\Facades\Route;
use EasyAI\LaravelAI\Chat\Controllers\AIChatController;

Route::prefix('ai-chat')->name('ai-chat.')->group(function () {
    Route::get('/', [AIChatController::class, 'index'])->name('index');

    Route::prefix('api')->group(function () {
        Route::post('sessions',             [AIChatController::class, 'newSession'])->name('sessions.new');
        Route::delete('sessions/{session}', [AIChatController::class, 'deleteSession'])->name('sessions.delete');
        Route::get('stream',                [AIChatController::class, 'stream'])->name('stream');
        Route::post('provider',             [AIChatController::class, 'switchProvider'])->name('provider');
    });
});
