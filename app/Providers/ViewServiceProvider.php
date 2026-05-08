<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Peminjaman;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Bagikan data ke semua view di folder kepala-pustaka
        View::composer('kepala-pustaka.*', function ($view) {
            $dendaPending = Peminjaman::where('status_verifikasi', 'pending')
                ->where('denda_total', '>', 0)
                ->count();
            
            $view->with('dendaPending', $dendaPending);
        });
    }
}