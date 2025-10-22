<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Filament;
use LaraZeus\Qr\QrServiceProvider;

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
        //

    }
}
