<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ManualMidtransService;
use App\Services\MidtransService;

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
        //
    }
}