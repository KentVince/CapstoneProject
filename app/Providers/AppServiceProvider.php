<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Filament\Filament;
use LaraZeus\Qr\QrServiceProvider;
use App\Models\Bulletin;
use App\Models\PestAndDisease;
use App\Models\SoilAnalysis;
use App\Models\SoilAnalysisExpertComment;
use App\Models\PestDiseaseExpertComment;
use App\Observers\BulletinObserver;
use App\Observers\PestAndDiseaseObserver;
use App\Observers\SoilAnalysisObserver;
use App\Observers\SoilAnalysisExpertCommentObserver;
use App\Observers\PestDiseaseExpertCommentObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
       //  $this->app->register(QrServiceProvider::class);

        // Ensure panel_user is always redirected to login after logout
        $this->app->bind(
            \Filament\Http\Responses\Auth\Contracts\LogoutResponse::class,
            fn () => new class implements \Filament\Http\Responses\Auth\Contracts\LogoutResponse {
                public function toResponse($request)
                {
                    return redirect('/admin/login');
                }
            }
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Register the PestAndDisease Observer for real-time notifications
        PestAndDisease::observe(PestAndDiseaseObserver::class);

        // Register the SoilAnalysis Observer for real-time notifications when syncing from Flutter
        SoilAnalysis::observe(SoilAnalysisObserver::class);

        // Register the Bulletin Observer — sends FCM to all mobile users when a bulletin is created
        Bulletin::observe(BulletinObserver::class);

        // Register SoilAnalysisExpertComment Observer — sends FCM to farmer on every new expert comment
        SoilAnalysisExpertComment::observe(SoilAnalysisExpertCommentObserver::class);

        // Register PestDiseaseExpertComment Observer — sends FCM to farmer on every new expert comment
        PestDiseaseExpertComment::observe(PestDiseaseExpertCommentObserver::class);
    }
}
