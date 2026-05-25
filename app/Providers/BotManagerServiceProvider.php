<?php

declare(strict_types=1);

namespace App\Providers;

use App\NotificationChannels\BotManager;
use Illuminate\Support\ServiceProvider;

class BotManagerServiceProvider extends ServiceProvider
{
    /**
     * Register the BotManager service.
     */
    public function register(): void
    {
        $this->app->singleton(BotManager::class, function () {
            return new BotManager();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
