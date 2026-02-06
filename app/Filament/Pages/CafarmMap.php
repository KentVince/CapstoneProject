<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\PestAndDisease;
use App\Models\PestAndDiseaseCategory;

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
        // Build a name → type lookup from categories
        $categoryTypes = PestAndDiseaseCategory::pluck('type', 'name');

        return PestAndDisease::select(
            'case_id',
            'latitude',
            'longitude',
            'pest',
            'confidence',
            'severity',
            'date_detected',
            'area',
            'image_path',
            'validation_status'
        )
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->get()
        ->map(function ($item) use ($categoryTypes) {
            if ($item->image_path) {
                $item->image_url = asset('storage/' . $item->image_path);
            } else {
                $item->image_url = null;
            }

            // Determine type from categories lookup
            $item->type = $categoryTypes[$item->pest] ?? 'pest';

            return $item;
        });
    }

    // Get unique pest/disease types for filtering
    public function getPestTypes()
    {
        return PestAndDisease::whereNotNull('pest')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->distinct()
            ->pluck('pest')
            ->sort()
            ->values();
    }

    // Get the first location for the map's initial view
    public function getInitialLocation()
    {
        return PestAndDisease::select('latitude', 'longitude')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->first();
    }

}
