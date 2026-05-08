<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ManualMidtransService;
use App\Services\MidtransService;

class MidtransServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            ManualMidtransService::class,
            function () {
                return new ManualMidtransService();
            }
        );

        $this->app->singleton(
            MidtransService::class,
            function ($app) {
                return new MidtransService(
                    $app->make(
                        ManualMidtransService::class
                    )
                );
            }
        );
    }

    public function boot(): void
    {
        //
    }
}