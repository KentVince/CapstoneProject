<?php

namespace App\Filament\Widgets;

use App\Models\PestAndDisease;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ValidationStatusChart extends ApexChartWidget
{
    protected static ?string $chartId = 'validationStatusChart';

    protected static ?string $heading = 'Detection Validation Status';

    protected function getOptions(): array
    {
        $statuses = PestAndDisease::selectRaw('validation_status, COUNT(*) as count')
            ->groupBy('validation_status')
            ->pluck('count', 'validation_status');

        $pending  = (int) ($statuses['pending']  ?? 0);
        $approved = (int) ($statuses['approved'] ?? 0);
        $rejected = (int) ($statuses['rejected'] ?? 0);

        return [
            'chart' => [
                'type'   => 'donut',
                'height' => 320,
            ],
            'series' => [$pending, $approved, $rejected],
            'labels' => ['Pending', 'Approved', 'Rejected'],
            'colors' => ['#f59e0b', '#22c55e', '#ef4444'],
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
                        'size'   => '65%',
                        'labels' => [
                            'show'  => true,
                            'total' => [
                                'show'  => true,
                                'label' => 'Total',
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
