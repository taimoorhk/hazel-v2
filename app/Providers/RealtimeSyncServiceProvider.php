<?php

namespace App\Providers;

use App\Services\RealtimeSyncService;
use Illuminate\Support\ServiceProvider;

class RealtimeSyncServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(RealtimeSyncService::class, function ($app) {
            return new RealtimeSyncService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Initialize real-time sync when the application boots
        $realtimeSync = $this->app->make(RealtimeSyncService::class);
        $realtimeSync->initializeRealtimeSync();
    }
} 