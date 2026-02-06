<?php

namespace App\Filament\Widgets;

use App\Models\Farm;
use App\Models\Farmer;
use App\Models\PestAndDisease;
use App\Models\SoilAnalysis;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatOverview extends BaseWidget
{

    protected function getStats(): array
    {
        // Get total farmers
        $totalFarmers = Farmer::count();

        // Get total farms and area
        $totalFarms = Farm::count();
        $totalArea = Farm::sum(DB::raw('CAST(lot_hectare AS DECIMAL(10,2))'));

        // Get pest & disease stats
        $totalCases = PestAndDisease::count();
        $approvedCases = PestAndDisease::where('validation_status', 'approved')->count();
        $criticalCases = PestAndDisease::where('severity', 'high')
            ->where('validation_status', 'approved')
            ->count();

        // Get soil analysis stats
        $totalSoilTests = SoilAnalysis::count();

        // Get recent cases trend (last 6 months)
        $casesTrend = PestAndDisease::selectRaw('COUNT(*) as count, MONTH(date_detected) as month')
            ->where('date_detected', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count')
            ->toArray();

        // Get farms trend (last 6 months)
        $farmsTrend = Farm::selectRaw('COUNT(*) as count, MONTH(created_at) as month')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count')
            ->toArray();

        return [
            Stat::make('Total Farmers', number_format($totalFarmers))
                ->description('Registered farmers')
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->chart($farmsTrend),

            Stat::make('Total Farms', number_format($totalFarms))
                ->description(number_format($totalArea, 2) . ' hectares')
                ->descriptionIcon('heroicon-m-map')
                ->color('primary')
                ->chart($farmsTrend),

            Stat::make('Pest & Disease Cases', number_format($totalCases))
                ->description($approvedCases . ' approved cases')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning')
                ->chart($casesTrend),

            Stat::make('Critical Cases', number_format($criticalCases))
                ->description('High severity')
                ->descriptionIcon('heroicon-m-shield-exclamation')
                ->color('danger'),

            Stat::make('Soil Tests', number_format($totalSoilTests))
                ->description('Analysis conducted')
                ->descriptionIcon('heroicon-m-beaker')
                ->color('info'),

            Stat::make('Average pH Level', number_format(SoilAnalysis::avg('ph_level') ?? 0, 2))
                ->description('Soil acidity level')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('success'),
        ];
    }
}
