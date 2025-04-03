<?php

use App\Http\Controllers\TrackController;
use App\Http\Controllers\ArtistController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'abilities:tracks:create'])->post('/tracks', [TrackController::class, 'store']);
Route::apiResource('tracks', TrackController::class)->except(['store']);


Route::middleware(['auth:sanctum', 'abilities:artists:create'])->post('/artists', [ArtistController::class, 'store']);
Route::apiResource('artists', ArtistController::class)->except(['store']);
