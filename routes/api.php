<?php

use App\Http\Controllers\Api\GistCardController;
use App\Http\Controllers\Api\PinnedRepoCardController;
use App\Http\Controllers\Api\StatsCardController;
use App\Http\Controllers\Api\StatusController;
use App\Http\Controllers\Api\TopLanguagesCardController;
use App\Http\Controllers\Api\WakatimeCardController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:60,1')->group(function (): void {
    Route::get('/', StatsCardController::class);
    Route::get('/top-langs', TopLanguagesCardController::class);
    Route::get('/pin', PinnedRepoCardController::class);
    Route::get('/gist', GistCardController::class);
    Route::get('/wakatime', WakatimeCardController::class);

    Route::prefix('status')->group(function (): void {
        Route::get('/up', [StatusController::class, 'up']);
        Route::get('/pat-info', [StatusController::class, 'patInfo']);
    });
});
