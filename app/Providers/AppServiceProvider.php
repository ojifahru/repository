<?php

namespace App\Providers;

use App\Listeners\AuthActivitySubscriber;
use App\Models\TriDharma;
use App\Observers\TriDharmaObserver;
use Illuminate\Support\Facades\Event;
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
        Event::subscribe(AuthActivitySubscriber::class);
        TriDharma::observe(TriDharmaObserver::class);
    }
}
