<?php

use Illuminate\Support\Facades\Route;

Route::post('webhook', [\App\Http\Controllers\telegram\TelegramController::class, 'webhook']);
