<?php

namespace App\Filament\Widgets;

use App\Models\PestAndDisease;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class TopAffectedBarangaysChart extends ApexChartWidget
{
    protected static ?string $chartId = 'topAffectedBarangaysChart';

    protected static ?string $heading = 'Top 5 Most Affected Barangays';

    protected static ?string $subheading = 'Ranked by approved detection count';

    protected int | string | array $columnSpan = 'full';

    protected function getOptions(): array
    {
        $data = PestAndDisease::selectRaw('area, COUNT(*) as count')
            ->where('validation_status', 'approved')
            ->whereNotNull('area')
            ->where('area', '!=', '')
            ->where('area', '!=', 'Unknown area')
            ->groupBy('area')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        $categories = $data->pluck('area')->toArray();
        $counts     = $data->pluck('count')->map(fn ($v) => (int) $v)->toArray();

        // Color gradient: darkest = most cases, lightest = fewest
        $colors = ['#dc2626', '#f97316', '#f59e0b', '#84cc16', '#22c55e'];

        return [
            'chart' => [
                'type'    => 'bar',
                'height'  => max(180, count($categories) * 58),
                'toolbar' => ['show' => false],
                'sparkline' => ['enabled' => false],
            ],
            'series' => [
                [
                    'name' => 'Detections',
                    'data' => $counts,
                ],
            ],
            'plotOptions' => [
                'bar' => [
                    'horizontal'       => true,
                    'borderRadius'     => 6,
                    'barHeight'        => '55%',
                    'distributed'      => true,
                    'dataLabels'       => ['position' => 'bottom'],
                ],
            ],
            'colors' => $colors,
            'dataLabels' => [
                'enabled'   => true,
                'textAnchor' => 'start',
                'style' => [
                    'fontSize'   => '12px',
                    'fontFamily' => 'inherit',
                    'fontWeight' => '600',
                    'colors'     => ['#fff'],
                ],
                'formatter' => 'function (val) { return val + " cases"; }',
                'offsetX' => 8,
            ],
            'xaxis' => [
                'categories' => $categories,
                'labels' => [
                    'show'  => false,
                ],
                'axisBorder' => ['show' => false],
                'axisTicks'  => ['show' => false],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                        'fontSize'   => '13px',
                        'fontWeight' => '500',
                    ],
                    'maxWidth' => 220,
                ],
            ],
            'legend' => ['show' => false],
            'grid' => [
                'xaxis' => ['lines' => ['show' => false]],
                'yaxis' => ['lines' => ['show' => false]],
                'padding' => ['left' => 0, 'right' => 16],
            ],
            'tooltip' => [
                'style' => ['fontFamily' => 'inherit'],
                'y'     => ['title' => ['formatter' => 'function () { return "Approved Detections:"; }']],
            ],
            'states' => [
                'hover' => ['filter' => ['type' => 'darken', 'value' => 0.85]],
            ],
        ];
    }
}
