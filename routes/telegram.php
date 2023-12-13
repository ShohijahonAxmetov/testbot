<?php

use Illuminate\Support\Facades\Route;

Route::get('setWebhook', [\App\Http\Controllers\telegram\TelegramController::class, 'setWebhook']);
Route::post('webhook', [\App\Http\Controllers\telegram\TelegramController::class, 'webhook']);
