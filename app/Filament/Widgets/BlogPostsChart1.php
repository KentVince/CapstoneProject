<?php

namespace App\Filament\Widgets;

use App\Models\PestAndDisease;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class BlogPostsChart1 extends ApexChartWidget
{
    protected static ?string $chartId = 'blogPostsChart1';

    protected static ?string $heading = 'Cases by Severity';

    protected function getOptions(): array
    {
        $severities = PestAndDisease::selectRaw('severity, COUNT(*) as count')
            ->where('validation_status', 'approved')
            ->groupBy('severity')
            ->pluck('count', 'severity');

        $low    = (int) ($severities['low']    ?? 0);
        $medium = (int) ($severities['medium'] ?? 0);
        $high   = (int) ($severities['high']   ?? 0);

        return [
            'chart' => [
                'type'   => 'donut',
                'height' => 320,
            ],
            'series' => [$low, $medium, $high],
            'labels' => ['Low', 'Medium', 'High'],
            'colors' => ['#22c55e', '#f59e0b', '#ef4444'],
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
