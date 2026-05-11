<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Hanya alias yang kita butuhkan
        $middleware->alias([
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
            'role' => \App\Http\Middleware\CheckRole::class,
            'log.activity' => \App\Http\Middleware\LogActivity::class,
        ]);
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withSchedule(function (Schedule $schedule) {
        // Auto-return expired digital loans setiap jam
        $schedule->command('digital:auto-return')->hourly();
        
        // Atau dengan class Command
        // $schedule->command(\App\Console\Commands\AutoReturnExpiredDigitalLoans::class)->hourly();

        // Cek booking hangus setiap jam (cek setiap menit untuk testing, ganti hourly setelah production)
        $schedule->command('bookings:check-expired')->hourly();

    })
    ->create();