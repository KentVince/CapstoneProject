<?php

namespace App\Filament\Pages;

use App\Models\Farm;
use Filament\Pages\Page;
use App\Models\PestAndDisease;
use Illuminate\Support\Facades\DB;
use App\Filament\Widgets\StatOverview;
use Filament\Forms\Components\Section;
use App\Filament\Widgets\BlogPostsChart;
use App\Filament\Widgets\BlogPostsChart1;
use App\Filament\Widgets\BlogPostsChart3;
use App\Models\Municipality;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFilters;

class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFilters;

    protected static ?string $navigationIcon = 'heroicon-o-home';
    // protected static string $view = 'filament.pages.dashboard';
    // protected static ?string $navigationLabel = 'Dashboard';

    public $pestAndDiseaseData = [];
    public $farmData = [];
    public $pestData = [];
    public $casesBySeverity = [];

    public function getCasesBySeverity()
    {
        return PestAndDisease::selectRaw('severity, COUNT(*) as count')
            // ->where('treatment_status', 'Active') // Filter for active cases
            ->groupBy('severity')
            ->pluck('count', 'severity');
    }

    public function getWidgets(): array
    {
        return [
            StatOverview::class,
            BlogPostsChart::make([
                'data' => ['test' => 'raymart']
            ]),
            BlogPostsChart1::make(),
            BlogPostsChart3::class
        ];
    }

    public function mount()
    {
        $this->farmData = Farm::selectRaw('COUNT(*) as count, MONTH(created_at) as month')
            ->groupBy('month')
            ->pluck('count', 'month');

            

        // $this->pestData = PestAndDisease::selectRaw('COUNT(*) as count, type')
        //     ->groupBy('type')
        //     ->pluck('count', 'type');

        $this->pestAndDiseaseData = DB::table('pest_and_disease')
            ->select(DB::raw("DATE_FORMAT(date_detected, '%Y-%m') as month"), DB::raw('COUNT(*) as total_cases'))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $this->casesBySeverity = $this->getCasesBySeverity();
    }


    // Filter Section

    protected function getHeaderActions(): array
    {
        // dd(! auth()->user()->isAdmin() ? auth()->user()->office_id: null);
        return [
            FilterAction::make()
                ->form([
                    Section::make()
                    ->schema([
                       Select::make('municipal')
                            ->options(Municipality::all()->pluck('municipality',  'code'))
                            ->columnSpanFull()
                    ])
                    
                    // ->hidden(fn() => ! auth()->user()->isHrAdmin())
                    ->columns(3),
                    // ...
                ])
                // ->fillForm([
                //     'office_id' => ! auth()->user()->isAdmin() ? auth()->user()->office_id: null,
                //     'startDate' => $this->filters['startDate'] ?? now()->startOfYear(),
                //     'endDate' => $this->filters['endDate'] ?? now(),
                //     'division_id' => $this->filters['division_id'] ?? null,
                //     'process_id' => $this->filters['process_id'] ?? null,
                //     'questionnaire_id' => $this->filters['questionnaire_id'] ?? null,
                //     'activity_id' => $this->filters['activity_id'] ?? null,
                //     'type' => $this->filters['type'] ?? null,
                // ])
                ,
        ];
    }

}

