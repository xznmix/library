<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ManualMidtransService;
use App\Services\MidtransService;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register Midtrans services
        $this->app->singleton(ManualMidtransService::class, function ($app) {
            return new ManualMidtransService();
        });
        
        $this->app->singleton(MidtransService::class, function ($app) {
            return new MidtransService($app->make(ManualMidtransService::class));
        });
    }

    public function boot(): void
    {
        if (env('APP_ENV') === 'production') {
        \URL::forceScheme('https');
    }
    }
}