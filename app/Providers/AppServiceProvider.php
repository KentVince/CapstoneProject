<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Filament;
use LaraZeus\Qr\QrServiceProvider;
use App\Models\PestAndDisease;
use App\Observers\PestAndDiseaseObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
       //  $this->app->register(QrServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register the PestAndDisease Observer for real-time notifications
        PestAndDisease::observe(PestAndDiseaseObserver::class);
    }
}
