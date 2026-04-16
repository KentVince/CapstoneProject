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
use App\Filament\Widgets\FarmsByMunicipalityChart;
use App\Filament\Widgets\SoilPhDistributionChart;
use App\Filament\Widgets\TopAffectedBarangaysChart;
use App\Filament\Widgets\ValidationStatusChart;
use App\Models\Municipality;
use Filament\Forms\Components\Select;
use Filament\Actions\Action;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFilters;

class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFilters;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';
    protected static string $view = 'filament.pages.dashboard';
    // protected static ?string $navigationLabel = 'Dashboard';

    /**
     * Hide dashboard from panel_user (default users)
     */
    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }
        
        // Hide from panel users
        return !$user->hasRole('panel_user');
    }

    public $pestAndDiseaseData = [];
    public $soilNutrientData = [];
    public $pestData = [];
    public $casesBySeverity = [];
    public $pestByBarangay = [];
    public $pestDistributionByBarangay = [];
    public $incidenceRateTrend = [];
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

    public function getPestByBarangay()
    {
        $query = PestAndDisease::selectRaw('area, COUNT(*) as count')
            ->where('validation_status', 'approved')
            ->whereNotNull('area')
            ->where('area', '!=', '')
            ->where('area', '!=', 'Unknown area');

        if ($startDate = $this->filters['startDate'] ?? null) {
            $query->where('date_detected', '>=', $startDate);
        }
        if ($endDate = $this->filters['endDate'] ?? null) {
            $query->where('date_detected', '<=', $endDate);
        }
        if ($municipal = $this->filters['municipal'] ?? null) {
            $query->where('area', $municipal);
        }

        $rows = $query->groupBy('area')
            ->orderByDesc('count')
            ->pluck('count', 'area');

        // Strip municipality from area (e.g. "Magcagong, Maragusan" → "Magcagong")
        $result = [];
        foreach ($rows as $area => $count) {
            $barangay = trim(explode(',', $area)[0]);
            $result[$barangay] = ($result[$barangay] ?? 0) + $count;
        }
        arsort($result);

        return $result;
    }

    public function getPestDistributionByBarangay()
    {
        $query = PestAndDisease::selectRaw('area, pest, COUNT(*) as count')
            ->where('validation_status', 'approved')
            ->whereNotNull('area')
            ->where('area', '!=', '')
            ->where('area', '!=', 'Unknown area');

        if ($startDate = $this->filters['startDate'] ?? null) {
            $query->where('date_detected', '>=', $startDate);
        }
        if ($endDate = $this->filters['endDate'] ?? null) {
            $query->where('date_detected', '<=', $endDate);
        }
        if ($municipal = $this->filters['municipal'] ?? null) {
            $query->where('area', $municipal);
        }

        $rows = $query->groupBy('area', 'pest')
            ->orderBy('area')
            ->get();

        // Collect all unique pests and build pivot: barangay => [pest => count]
        // Strip municipality from area (e.g. "Magcagong, Maragusan" → "Magcagong")
        $pests = [];
        $pivot = [];
        foreach ($rows as $row) {
            $barangay = trim(explode(',', $row->area)[0]);
            $pests[$row->pest] = true;
            $pivot[$barangay][$row->pest] = ($pivot[$barangay][$row->pest] ?? 0) + (int) $row->count;
        }

        // Sort pests alphabetically
        $pestList = array_keys($pests);
        sort($pestList);

        return [
            'pests' => $pestList,
            'pivot' => $pivot,
        ];
    }

    public function getIncidenceRateTrend()
    {
        $query = PestAndDisease::select(
                DB::raw("DATE_FORMAT(date_detected, '%Y-%m') as month"),
                DB::raw('AVG(pest_severity_pct) as avg_severity'),
                DB::raw('AVG(incidence_rating) as avg_incidence')
            )
            ->where('validation_status', 'approved')
            ->whereNotNull('date_detected');

        if ($startDate = $this->filters['startDate'] ?? null) {
            $query->where('date_detected', '>=', $startDate);
        }
        if ($endDate = $this->filters['endDate'] ?? null) {
            $query->where('date_detected', '<=', $endDate);
        }
        if ($municipal = $this->filters['municipal'] ?? null) {
            $query->where('area', $municipal);
        }

        return $query->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();
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
            ->limit(10)
            ->pluck('count', 'pest');
    }

    public function getFarmsByMunicipality()
    {
        $query = Farm::select('farmer_address_mun', DB::raw('COUNT(*) as count'));

        // Apply municipality filter if exists
        if ($municipal = $this->filters['municipal'] ?? null) {
            $query->where('farmer_address_mun', $municipal);
        }

        return $query->groupBy('farmer_address_mun')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'farmer_address_mun');
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
        ->orderBy('date_detected', 'desc');

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
            'farmer:id,first_name,last_name'
        ])
        ->orderBy('created_at', 'desc');

        // Don't apply filters for table data - show all recent records
        // Filters are for charts only

        return $query->get()->map(function ($farm) use ($barangays) {
            $farm->farmer_name = $farm->farmer ?
                ($farm->farmer->first_name . ' ' . $farm->farmer->last_name) :
                'N/A';
            // Get barangay name from lookup
            $farm->barangay_name = $barangays[$farm->farmer_address_bgy] ?? $farm->farmer_address_bgy;
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
        ->orderBy('date_collected', 'desc');

        return $query->get();
    }

    // Get recent farmers for table
    public function getRecentFarmers()
    {
        // Get all municipalities for mapping
        $municipalities = Municipality::all()->pluck('municipality', 'municipality')->toArray();

        $query = Farmer::with('barangayData')
        ->orderBy('created_at', 'desc');

        // Don't apply filters for table data - show all recent records
        // Filters are for charts only

        return $query->get()->map(function ($farmer) use ($municipalities) {
            $farmer->full_name = trim(($farmer->first_name ?? '') . ' ' . ($farmer->middle_name ?? '') . ' ' . ($farmer->last_name ?? ''));
            // Get barangay name from relationship
            $farmer->barangay_name = $farmer->barangayData ? $farmer->barangayData->barangay : $farmer->farmer_address_bgy;
            // Get municipality name from lookup
            $farmer->municipality_name = $municipalities[$farmer->farmer_address_mun] ?? $farmer->farmer_address_mun;
            return $farmer;
        });
    }

    // Get statistics for cards
    public function getTotalPests()
    {
        return PestAndDisease::where('validation_status', 'approved')
            ->where('type', 'pest')
            ->count();
    }

    public function getTotalDiseases()
    {
        return PestAndDisease::where('validation_status', 'approved')
            ->where('type', 'disease')
            ->count();
    }

    public function getTotalFarmers()
    {
        return Farmer::count();
    }

    public function getTotalSoilTested()
    {
        return SoilAnalysis::count();
    }

    public function getPendingPestCases()
    {
        return PestAndDisease::where('validation_status', 'pending')->count();
    }

    public function getPendingSoilAnalyses()
    {
        return SoilAnalysis::where('validation_status', 'pending')->count();
    }

    public function getOldestPendingPestDate()
    {
        $record = PestAndDisease::where('validation_status', 'pending')
            ->orderBy('date_detected', 'asc')
            ->first();
        return $record ? $record->date_detected : null;
    }

    public function getOldestPendingSoilDate()
    {
        $record = SoilAnalysis::where('validation_status', 'pending')
            ->orderBy('date_collected', 'asc')
            ->first();
        return $record ? $record->date_collected : null;
    }

    public function getPendingPestRecords()
    {
        return PestAndDisease::with('farmer:id,first_name,last_name')
            ->where('validation_status', 'pending')
            ->orderBy('date_detected', 'asc')
            ->limit(5)
            ->get(['case_id', 'farmer_id', 'pest', 'severity', 'date_detected', 'area', 'confidence']);
    }

    public function getPendingSoilRecords()
    {
        return SoilAnalysis::with('farmer:id,first_name,last_name')
            ->where('validation_status', 'pending')
            ->orderBy('date_collected', 'asc')
            ->limit(5)
            ->get(['id', 'farmer_id', 'farm_name', 'date_collected', 'ph_level', 'nitrogen', 'phosphorus', 'potassium']);
    }

    public function getWidgets(): array
    {
        return [
            StatOverview::class,
            BlogPostsChart::class,
            BlogPostsChart1::class,
            ValidationStatusChart::class,
            BlogPostsChart3::class,
            FarmsByMunicipalityChart::class,
            SoilPhDistributionChart::class,
            TopAffectedBarangaysChart::class,
        ];
    }

    public function mount()
    {
        // Soil Nutrient Levels per Barangay
        $soilQuery = SoilAnalysis::join('farms', 'soil_analysis.farm_id', '=', 'farms.id')
            ->join('barangays', 'farms.farmer_address_bgy', '=', 'barangays.code')
            ->whereNotNull('farms.farmer_address_bgy')
            ->where('farms.farmer_address_bgy', '!=', '')
            ->selectRaw('barangays.barangay as barangay_name,
                AVG(soil_analysis.nitrogen) as avg_nitrogen,
                AVG(soil_analysis.phosphorus) as avg_phosphorus,
                AVG(soil_analysis.potassium) as avg_potassium,
                AVG(soil_analysis.organic_matter) as avg_organic_matter')
            ->groupBy('barangays.barangay')
            ->orderBy('barangay_name');

        if ($municipal = $this->filters['municipal'] ?? null) {
            $soilQuery->where('farms.farmer_address_mun', $municipal);
        }

        $this->soilNutrientData = $soilQuery->get();

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
        $this->pestByBarangay = $this->getPestByBarangay();
        $this->pestDistributionByBarangay = $this->getPestDistributionByBarangay();
        $this->incidenceRateTrend = $this->getIncidenceRateTrend();
        $this->topPests = $this->getTopPests();
        $this->farmsByMunicipality = $this->getFarmsByMunicipality();
        $this->soilPhDistribution = $this->getSoilPhDistribution();
    }


    // Filter Section

    protected function getHeaderActions(): array
    {
        return [
            Action::make('printReport')
                ->label('Print Report')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn () => route('dashboard.print-report', array_filter([
                    'startDate' => $this->filters['startDate'] ?? now()->startOfYear()->format('Y-m-d'),
                    'endDate'   => $this->filters['endDate']   ?? now()->format('Y-m-d'),
                    'municipal' => $this->filters['municipal'] ?? null,
                ])))
                ->openUrlInNewTab(),

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
                ]),
        ];
    }

}

