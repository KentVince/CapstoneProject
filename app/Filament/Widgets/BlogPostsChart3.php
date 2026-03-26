<?php

namespace App\Filament\Widgets;

use App\Models\PestAndDisease;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class BlogPostsChart3 extends ApexChartWidget
{
    protected static ?string $chartId = 'blogPostsChart3';

    protected static ?string $heading = 'Top 10 Pests & Diseases Detected';

    protected int | string | array $columnSpan = 'full';

    protected function getOptions(): array
    {
        $topItems = PestAndDisease::selectRaw('pest, COUNT(*) as count')
            ->where('validation_status', 'approved')
            ->groupBy('pest')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        $categories = $topItems->pluck('pest')->toArray();
        $data       = $topItems->pluck('count')->map(fn ($v) => (int) $v)->toArray();

        return [
            'chart' => [
                'type'    => 'bar',
                'height'  => 380,
                'toolbar' => ['show' => false],
            ],
            'series' => [
                [
                    'name' => 'Detections',
                    'data' => $data,
                ],
            ],
            'xaxis' => [
                'categories' => $categories,
                'labels'     => [
                    'style' => ['fontFamily' => 'inherit'],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => ['fontFamily' => 'inherit'],
                ],
            ],
            'plotOptions' => [
                'bar' => [
                    'horizontal'   => true,
                    'borderRadius' => 4,
                    'barHeight'    => '60%',
                ],
            ],
            'colors'     => ['#3b82f6'],
            'dataLabels' => [
                'enabled' => true,
                'style'   => ['fontFamily' => 'inherit'],
            ],
            'grid' => [
                'borderColor' => '#e5e7eb',
            ],
            'tooltip' => [
                'style' => ['fontFamily' => 'inherit'],
            ],
        ];
    }
}
