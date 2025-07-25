<?php

namespace App\Providers;

use App\Services\PhoneService;
use Illuminate\Support\ServiceProvider;

class PhoneProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(PhoneService::class, function ($app) {
            return new PhoneService();
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
