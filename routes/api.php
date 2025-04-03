<?php

use App\Http\Controllers\TrackController;
use App\Http\Controllers\ArtistController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'abilities:tracks:create'])->post('/tracks', [TrackController::class, 'store']);

// For other routes, you can use the apiResource method
Route::apiResource('tracks', TrackController::class)->except(['store']);
Route::apiResource('artists', ArtistController::class);
