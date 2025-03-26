<?php

use App\Http\Controllers\TelegramProxyController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get("/", function () {
    return Inertia::render("Home");
});

Route::get('/proxy/telegram/{file}', [TelegramProxyController::class, 'fetchFile'])
    ->where('file', '.*');