<?php

namespace App\Filament\Widgets;

use App\Models\SoilAnalysis;
use Illuminate\Support\Facades\DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class SoilPhDistributionChart extends ApexChartWidget
{
    protected static ?string $chartId = 'soilPhDistributionChart';

    protected static ?string $heading = 'Soil pH Distribution';

    protected function getOptions(): array
    {
        $distribution = SoilAnalysis::select(
            DB::raw('CASE
                WHEN ph_level < 5.5 THEN "Very Acidic (< 5.5)"
                WHEN ph_level >= 5.5 AND ph_level < 6.0 THEN "Acidic (5.5–6.0)"
                WHEN ph_level >= 6.0 AND ph_level < 6.5 THEN "Slightly Acidic (6.0–6.5)"
                WHEN ph_level >= 6.5 AND ph_level < 7.0 THEN "Neutral (6.5–7.0)"
                ELSE "Alkaline (> 7.0)"
            END as ph_range'),
            DB::raw('COUNT(*) as count')
        )->whereNotNull('ph_level')
         ->groupBy('ph_range')
         ->pluck('count', 'ph_range');

        $labels = [
            'Very Acidic (< 5.5)',
            'Acidic (5.5–6.0)',
            'Slightly Acidic (6.0–6.5)',
            'Neutral (6.5–7.0)',
            'Alkaline (> 7.0)',
        ];

        $series = collect($labels)->map(fn ($l) => (int) ($distribution[$l] ?? 0))->toArray();

        return [
            'chart' => [
                'type'   => 'donut',
                'height' => 320,
            ],
            'series' => $series,
            'labels' => $labels,
            'colors' => ['#dc2626', '#f97316', '#eab308', '#22c55e', '#3b82f6'],
            'legend' => [
                'position' => 'bottom',
                'labels'   => ['fontFamily' => 'inherit'],
            ],
            'dataLabels' => [
                'style' => ['fontFamily' => 'inherit'],
            ],
            'plotOptions' => [
                'pie' => [
                    'donut' => [
                        'size'   => '60%',
                        'labels' => [
                            'show'  => true,
                            'total' => [
                                'show'  => true,
                                'label' => 'Samples',
                                'style' => ['fontFamily' => 'inherit'],
                            ],
                        ],
                    ],
                ],
            ],
            'tooltip' => [
                'style' => ['fontFamily' => 'inherit'],
            ],
        ];
    }
}
