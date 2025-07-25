<?php

namespace App\Providers;

use App\Services\PhotoService;
use Illuminate\Support\ServiceProvider;

class GroupProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(PhotoService::class, function ($app) {
            return new PhotoService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
