<?php

namespace App\Providers;

use App\Models\TriDharma;
use App\Observers\TriDharmaObserver;
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
        TriDharma::observe(TriDharmaObserver::class);
    }
}
