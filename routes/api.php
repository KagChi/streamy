<?php

use App\Http\Controllers\ArtistController;
use App\Http\Controllers\TrackController;
use Illuminate\Support\Facades\Route;

Route::apiResource('tracks', TrackController::class);
Route::apiResource('artists', ArtistController::class);
