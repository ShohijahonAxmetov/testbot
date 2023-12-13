<?php

namespace App\Providers;

use App\Http\Controllers\telegram\Commands\StartCommand;

use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
