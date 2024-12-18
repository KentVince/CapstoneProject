<?php

namespace App\Filament\Pages;

use App\Models\Farm;
use Filament\Pages\Page;
use App\Models\PestAndDisease;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';
    protected static ?string $navigationLabel = 'Dashboard';
    // protected static ?string $navigationGroup = 'Admin';


    public function getFarmData()
    {
        return Farm::selectRaw('COUNT(*) as count, MONTH(created_at) as month')
            ->groupBy('month')
            ->pluck('count', 'month');
    }

    public function getPestData()
    {
        return PestAndDisease::selectRaw('COUNT(*) as count, type')
            ->groupBy('type')
            ->pluck('count', 'type');
    }


}
