<?php

namespace App\Filament\Widgets;

use App\Models\PestAndDisease;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class BlogPostsChart extends ApexChartWidget
{
    protected static ?string $chartId = 'blogPostsChart';

    protected static ?string $heading = 'Monthly Pest & Disease Detections';

    protected int | string | array $columnSpan = 'full';

    protected function getOptions(): array
    {
        // Build last 12 months labels and data
        $months = collect(range(11, 0))->map(fn ($i) => now()->subMonths($i));

        $labels = $months->map(fn ($m) => $m->format('M Y'))->toArray();

        $data = $months->map(fn ($m) =>
            PestAndDisease::whereYear('date_detected', $m->year)
                ->whereMonth('date_detected', $m->month)
                ->where('validation_status', 'approved')
                ->count()
        )->toArray();

        return [
            'chart' => [
                'type'    => 'bar',
                'height'  => 320,
                'toolbar' => ['show' => false],
            ],
            'series' => [
                [
                    'name' => 'Approved Cases',
                    'data' => $data,
                ],
            ],
            'xaxis' => [
                'categories' => $labels,
                'labels'     => [
                    'style' => ['fontFamily' => 'inherit'],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => ['fontFamily' => 'inherit'],
                ],
                'min' => 0,
            ],
            'colors'      => ['#f59e0b'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 4,
                    'columnWidth'  => '55%',
                ],
            ],
            'dataLabels' => ['enabled' => false],
            'grid' => [
                'borderColor' => '#e5e7eb',
            ],
            'tooltip' => [
                'style' => ['fontFamily' => 'inherit'],
            ],
        ];
    }
}
