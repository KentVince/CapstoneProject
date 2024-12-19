<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatOverview extends BaseWidget
{

    protected function getStats(): array
    {
        return [
            Stat::make('Unique views', '192.1k')
                ->description('testssdf')
                ->descriptionColor('danger')
                ->chart([100,50,20,80,10])
                ->chartColor('danger'),
            Stat::make('Bounce rate', '21%'),
            Stat::make('Average time on page', '3:12'),
        ];
    }
}
