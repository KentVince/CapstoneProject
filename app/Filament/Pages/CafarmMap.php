<?php

namespace App\Filament\Pages;

use App\Models\Farm;
use Filament\Pages\Page;
use App\Models\PestAndDisease;

class CafarmMap extends Page
{
    // protected static ?string $navigationIcon = 'heroicon-o-document-text';

    // protected static string $view = 'filament.pages.cafarm-map';


    protected static ?string $navigationLabel = 'CAFARM Map';
    protected static ?string $navigationGroup = 'Maps';
    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'cafarm-map';
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static string $view = 'filament.pages.cafarm-map';

    public function getPestAndDiseaseCases()
    {
        return PestAndDisease::select('latitude', 'longitude', 'pest', 'type')->get();
    }

    // Get the first location for the map's initial view
    public function getInitialLocation()
    {
        return PestAndDisease::select('latitude', 'longitude')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->first();
    }

    // Fetch all registered farms
    public function getFarms()
    {
        return Farm::select('latitude', 'longitude', 'name')->get();
    }


}
