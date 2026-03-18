<?php

namespace App\Filament\Widgets;

use App\Models\Farm;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class FarmsByMunicipalityChart extends ApexChartWidget
{
    protected static ?string $chartId = 'farmsByMunicipalityChart';

    protected static ?string $heading = 'Farms by Municipality';

    protected function getOptions(): array
    {
        $data = Farm::selectRaw('farmer_address_mun, COUNT(*) as count')
            ->whereNotNull('farmer_address_mun')
            ->where('farmer_address_mun', '!=', '')
            ->groupBy('farmer_address_mun')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return [
            'chart' => [
                'type'    => 'bar',
                'height'  => 320,
                'toolbar' => ['show' => false],
            ],
            'series' => [
                [
                    'name' => 'Farms',
                    'data' => $data->pluck('count')->map(fn ($v) => (int) $v)->toArray(),
                ],
            ],
            'xaxis' => [
                'categories' => $data->pluck('farmer_address_mun')->toArray(),
                'labels'     => [
                    'style'  => ['fontFamily' => 'inherit'],
                    'rotate' => -30,
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => ['fontFamily' => 'inherit'],
                ],
                'min' => 0,
            ],
            'colors'      => ['#22c55e'],
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
