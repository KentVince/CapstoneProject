<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\PestAndDisease;
use App\Models\PestAndDiseaseCategory;
use App\Models\SoilAnalysis;
use App\Models\Farm;
use App\Models\Municipality;
use App\Models\Barangay;

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

    /**
     * Hide CafarmMap from panel_user (default users)
     */
    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }
        
        // Hide from panel users
        return !$user->hasRole('panel_user');
    }

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
        ->where('validation_status', 'approved') // Only show approved records
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

    // Get pests only (from categories)
    public function getPestsList()
    {
        return PestAndDiseaseCategory::where('type', 'pest')
            ->whereNotNull('name')
            ->distinct()
            ->pluck('name')
            ->sort()
            ->values();
    }

    // Get diseases only (from categories)
    public function getDiseasesList()
    {
        return PestAndDiseaseCategory::where('type', 'disease')
            ->whereNotNull('name')
            ->distinct()
            ->pluck('name')
            ->sort()
            ->values();
    }

    // Get soil analysis data with farm coordinates
    public function getSoilAnalysisLocations()
    {
        return SoilAnalysis::select(
            'soil_analysis.id',
            'soil_analysis.farm_id',
            'soil_analysis.farm_name',
            'soil_analysis.date_collected',
            'soil_analysis.ph_level',
            'soil_analysis.nitrogen',
            'soil_analysis.phosphorus',
            'soil_analysis.potassium',
            'soil_analysis.organic_matter',
            'soil_analysis.recommendation',
            'farms.latitude',
            'farms.longitude',
            'farms.barangay',
            'farms.municipality'
        )
        ->join('farms', 'soil_analysis.farm_id', '=', 'farms.id')
        ->whereNotNull('farms.latitude')
        ->whereNotNull('farms.longitude')
        ->get()
        ->map(function ($item) {
            return $item;
        });
    }

    // Get all registered farms
    public function getAllFarms()
    {
        $municipalities = Municipality::pluck('municipality', 'code');
        $barangays = Barangay::pluck('barangay', 'code');

        return Farm::select('id', 'name', 'latitude', 'longitude', 'barangay', 'municipality')
            ->orderBy('name')
            ->get()
            ->map(function ($farm) use ($municipalities, $barangays) {
                $farm->municipality_name = $municipalities[$farm->municipality] ?? $farm->municipality;
                $farm->barangay_name = $barangays[$farm->barangay] ?? $farm->barangay;
                return $farm;
            });
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
