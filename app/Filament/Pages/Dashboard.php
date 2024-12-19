<?php

namespace App\Filament\Pages;

use App\Models\Farm;
use App\Models\PestAndDisease;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.dashboard';
    protected static ?string $navigationLabel = 'Dashboard';

    public $pestAndDiseaseData = [];
    public $farmData = [];
    public $pestData = [];
    public $casesBySeverity = [];

    public function getCasesBySeverity()
    {
        return PestAndDisease::selectRaw('severity, COUNT(*) as count')
            ->where('treatment_status', 'Active') // Filter for active cases
            ->groupBy('severity')
            ->pluck('count', 'severity');
    }

    public function mount()
    {
        $this->farmData = Farm::selectRaw('COUNT(*) as count, MONTH(created_at) as month')
            ->groupBy('month')
            ->pluck('count', 'month');

            

        $this->pestData = PestAndDisease::selectRaw('COUNT(*) as count, type')
            ->groupBy('type')
            ->pluck('count', 'type');

        $this->pestAndDiseaseData = DB::table('pest_and_disease')
            ->select(DB::raw("DATE_FORMAT(date_detected, '%Y-%m') as month"), DB::raw('COUNT(*) as total_cases'))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $this->casesBySeverity = $this->getCasesBySeverity();
    }



}

