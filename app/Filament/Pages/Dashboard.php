<?php

namespace App\Filament\Pages;

use App\Models\Farm;
use App\Models\Farmer;
use App\Models\PestAndDisease;
use App\Models\SoilAnalysis;
use App\Models\PestAndDiseaseCategory;
use Illuminate\Support\Facades\DB;
use App\Filament\Widgets\StatOverview;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\DatePicker;
use App\Filament\Widgets\BlogPostsChart;
use App\Filament\Widgets\BlogPostsChart1;
use App\Filament\Widgets\BlogPostsChart3;
use App\Models\Municipality;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFilters;

class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFilters;

    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.dashboard';
    // protected static ?string $navigationLabel = 'Dashboard';

    public $pestAndDiseaseData = [];
    public $farmData = [];
    public $pestData = [];
    public $casesBySeverity = [];
    public $pestVsDisease = [];
    public $topPests = [];
    public $farmsByMunicipality = [];
    public $soilPhDistribution = [];
    public $monthlyComparison = [];

    public function getCasesBySeverity()
    {
        $query = PestAndDisease::selectRaw('severity, COUNT(*) as count')
            ->where('validation_status', 'approved');

        // Apply date filter if exists
        if ($startDate = $this->filters['startDate'] ?? null) {
            $query->where('date_detected', '>=', $startDate);
        }
        if ($endDate = $this->filters['endDate'] ?? null) {
            $query->where('date_detected', '<=', $endDate);
        }

        // Apply municipality filter if exists (using area column)
        if ($municipal = $this->filters['municipal'] ?? null) {
            $query->where('area', $municipal);
        }

        return $query->groupBy('severity')->pluck('count', 'severity');
    }

    public function getPestVsDisease()
    {
        // Get type from categories
        $categoryTypes = PestAndDiseaseCategory::pluck('type', 'name');

        $query = PestAndDisease::select('pest', DB::raw('COUNT(*) as count'))
            ->where('validation_status', 'approved');

        // Apply filters
        if ($startDate = $this->filters['startDate'] ?? null) {
            $query->where('date_detected', '>=', $startDate);
        }
        if ($endDate = $this->filters['endDate'] ?? null) {
            $query->where('date_detected', '<=', $endDate);
        }
        if ($municipal = $this->filters['municipal'] ?? null) {
            $query->where('area', $municipal);
        }

        $data = $query->groupBy('pest')->get();

        $pestCount = 0;
        $diseaseCount = 0;

        foreach ($data as $item) {
            $type = $categoryTypes[$item->pest] ?? 'pest';
            if ($type === 'pest') {
                $pestCount += $item->count;
            } else {
                $diseaseCount += $item->count;
            }
        }

        return [
            'Pests' => $pestCount,
            'Diseases' => $diseaseCount
        ];
    }

    public function getTopPests()
    {
        $query = PestAndDisease::select('pest', DB::raw('COUNT(*) as count'))
            ->where('validation_status', 'approved');

        // Apply filters
        if ($startDate = $this->filters['startDate'] ?? null) {
            $query->where('date_detected', '>=', $startDate);
        }
        if ($endDate = $this->filters['endDate'] ?? null) {
            $query->where('date_detected', '<=', $endDate);
        }
        if ($municipal = $this->filters['municipal'] ?? null) {
            $query->where('area', $municipal);
        }

        return $query->groupBy('pest')
            ->orderByDesc('count')
            ->limit(5)
            ->pluck('count', 'pest');
    }

    public function getFarmsByMunicipality()
    {
        $query = Farm::select('municipality', DB::raw('COUNT(*) as count'));

        // Apply municipality filter if exists
        if ($municipal = $this->filters['municipal'] ?? null) {
            $query->where('municipality', $municipal);
        }

        return $query->groupBy('municipality')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'municipality');
    }

    public function getSoilPhDistribution()
    {
        $query = SoilAnalysis::select(
            DB::raw('CASE
                WHEN ph_level < 5.5 THEN "Very Acidic (< 5.5)"
                WHEN ph_level >= 5.5 AND ph_level < 6.0 THEN "Acidic (5.5-6.0)"
                WHEN ph_level >= 6.0 AND ph_level < 6.5 THEN "Slightly Acidic (6.0-6.5)"
                WHEN ph_level >= 6.5 AND ph_level < 7.0 THEN "Neutral (6.5-7.0)"
                ELSE "Alkaline (> 7.0)"
            END as ph_range'),
            DB::raw('COUNT(*) as count')
        )->whereNotNull('ph_level');

        return $query->groupBy('ph_range')->pluck('count', 'ph_range');
    }

    // Get recent pest and disease cases for table
    public function getRecentPestDiseases()
    {
        $categoryTypes = PestAndDiseaseCategory::pluck('type', 'name');

        $query = PestAndDisease::select(
            'case_id',
            'pest',
            'confidence',
            'severity',
            'date_detected',
            'area',
            'validation_status'
        )
        ->where('validation_status', 'approved')
        ->orderBy('date_detected', 'desc')
        ->limit(50);

        // Don't apply filters for table data - show all recent records
        // Filters are for charts only

        return $query->get()->map(function ($item) use ($categoryTypes) {
            $item->type = $categoryTypes[$item->pest] ?? 'pest';
            return $item;
        });
    }

    // Get recent farms for table
    public function getRecentFarms()
    {
        // Get all barangays for mapping
        $barangays = \App\Models\Barangay::all()->pluck('barangay', 'code')->toArray();

        $query = Farm::with([
            'farmer:id,firstname,lastname'
        ])
        ->orderBy('created_at', 'desc')
        ->limit(50);

        // Don't apply filters for table data - show all recent records
        // Filters are for charts only

        return $query->get()->map(function ($farm) use ($barangays) {
            $farm->farmer_name = $farm->farmer ?
                ($farm->farmer->firstname . ' ' . $farm->farmer->lastname) :
                'N/A';
            // Get barangay name from lookup
            $farm->barangay_name = $barangays[$farm->barangay] ?? $farm->barangay;
            return $farm;
        });
    }

    // Get recent soil analysis for table
    public function getRecentSoilAnalysis()
    {
        $query = SoilAnalysis::select(
            'id',
            'farm_name',
            'date_collected',
            'ph_level',
            'nitrogen',
            'phosphorus',
            'potassium',
            'organic_matter'
        )
        ->orderBy('date_collected', 'desc')
        ->limit(50);

        return $query->get();
    }

    // Get recent farmers for table
    public function getRecentFarmers()
    {
        // Get all municipalities for mapping
        $municipalities = Municipality::all()->pluck('municipality', 'municipality')->toArray();

        $query = Farmer::with('barangayData')
        ->orderBy('created_at', 'desc')
        ->limit(50);

        // Don't apply filters for table data - show all recent records
        // Filters are for charts only

        return $query->get()->map(function ($farmer) use ($municipalities) {
            $farmer->full_name = trim(($farmer->firstname ?? '') . ' ' . ($farmer->middlename ?? '') . ' ' . ($farmer->lastname ?? ''));
            // Get barangay name from relationship
            $farmer->barangay_name = $farmer->barangayData ? $farmer->barangayData->barangay : $farmer->barangay;
            // Get municipality name from lookup
            $farmer->municipality_name = $municipalities[$farmer->municipality] ?? $farmer->municipality;
            return $farmer;
        });
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
        // Farms by month
        $query = Farm::selectRaw('COUNT(*) as count, MONTH(created_at) as month');

        if ($startDate = $this->filters['startDate'] ?? null) {
            $query->where('created_at', '>=', $startDate);
        }
        if ($endDate = $this->filters['endDate'] ?? null) {
            $query->where('created_at', '<=', $endDate);
        }
        if ($municipal = $this->filters['municipal'] ?? null) {
            $query->where('municipality', $municipal);
        }

        $this->farmData = $query->groupBy('month')->pluck('count', 'month');

        // Pest and Disease cases over time
        $query = DB::table('pest_and_disease')
            ->select(DB::raw("DATE_FORMAT(date_detected, '%Y-%m') as month"), DB::raw('COUNT(*) as total_cases'))
            ->where('validation_status', 'approved');

        if ($startDate = $this->filters['startDate'] ?? null) {
            $query->where('date_detected', '>=', $startDate);
        }
        if ($endDate = $this->filters['endDate'] ?? null) {
            $query->where('date_detected', '<=', $endDate);
        }
        if ($municipal = $this->filters['municipal'] ?? null) {
            $query->where('area', $municipal);
        }

        $this->pestAndDiseaseData = $query->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Load other analytics data
        $this->casesBySeverity = $this->getCasesBySeverity();
        $this->pestVsDisease = $this->getPestVsDisease();
        $this->topPests = $this->getTopPests();
        $this->farmsByMunicipality = $this->getFarmsByMunicipality();
        $this->soilPhDistribution = $this->getSoilPhDistribution();
    }


    // Filter Section

    protected function getHeaderActions(): array
    {
        return [
            FilterAction::make()
                ->form([
                    Section::make()
                    ->schema([
                        DatePicker::make('startDate')
                            ->label('Start Date')
                            ->default(now()->startOfYear())
                            ->maxDate(fn ($get) => $get('endDate') ?: now()),

                        DatePicker::make('endDate')
                            ->label('End Date')
                            ->default(now())
                            ->minDate(fn ($get) => $get('startDate'))
                            ->maxDate(now()),

                        Select::make('municipal')
                            ->label('Municipality')
                            ->options(Municipality::all()->pluck('municipality', 'municipality'))
                            ->searchable()
                            ->columnSpanFull()
                    ])
                    ->columns(2),
                ])
                ,
        ];
    }

}

