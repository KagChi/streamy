<?php

use App\Http\Controllers\TrackController;
use Illuminate\Support\Facades\Route;

Route::apiResource('tracks', TrackController::class);
