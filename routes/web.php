<?php

use App\Models\Farm;
use App\Models\Farmer;
use App\Models\SoilAnalysis;
use App\Models\PestAndDisease;
use App\Helpers\QRCodeGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Controllers\PestAndDiseaseController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('landing');
});

// Lightweight endpoint for sidebar badge polling
Route::get('/admin/api/pending-detections-count', function () {
    return response()->json([
        'count' => PestAndDisease::where('validation_status', 'pending')->count(),
    ]);
})->middleware(['web', 'auth'])->name('admin.pending-count');

// Impersonate routes
Route::impersonate();

// Override the Filament Impersonate leave route with a working implementation
Route::get('/filament-impersonate/leave', function () {
    auth()->user()->leaveImpersonation();
    return redirect('/admin/users');
})->name('filament-impersonate.leave')->middleware('web');

Route::get('/test-qr', function () {
    $data = json_encode([
        'Case ID' => 1,
        'Severity' => 'High',
        'Date Detected' => '2024-12-21',
    ]);

    return QRCodeGenerator::generate($data);
});



// Route::get('/pest-and-disease/{id}/qrcode', function ($id) {
//     $case = PestAndDisease::where('case_id', $id)->firstOrFail(); // Use case_id to find the record

//     // Data to encode in the QR code
//     $qrData = [
//         'Case ID' => $case->case_id,
//         'Severity' => $case->severity,
//         'Date Detected' => $case->date_detected,
//         'Location' => $case->location, // Adjust based on your schema
//     ];

//     // Generate QR Code
//     return response(
//         QrCode::size(300)->format('png')->generate(json_encode($qrData))
//     )->header('Content-Type', 'image/png');
// })->name('pest-disease.qrcode');

// ─── Dashboard Print Report ───────────────────────────────────────────────────
Route::get('/admin/print-report', function (Request $request) {

    $startDate = $request->get('startDate', now()->startOfYear()->format('Y-m-d'));
    $endDate   = $request->get('endDate',   now()->format('Y-m-d'));
    $municipal = $request->get('municipal');

    // ── Summary statistics (all-time totals, not filtered) ──────────────────
    $totalFarmers  = Farmer::count();
    $totalFarms    = Farm::count();
    $totalArea     = (float) Farm::sum(DB::raw('CAST(lot_hectare AS DECIMAL(10,2))'));
    $totalCases    = PestAndDisease::count();
    $approvedCases = PestAndDisease::where('validation_status', 'approved')->count();
    $criticalCases = PestAndDisease::where('severity', 'high')
                        ->where('validation_status', 'approved')->count();
    $totalSoilTests = SoilAnalysis::count();
    $avgPh          = round((float) SoilAnalysis::avg('ph_level'), 2);

    // ── Helpers: apply date + municipality scope ─────────────────────────────
    $scopePD = function ($query) use ($startDate, $endDate, $municipal) {
        $query->whereBetween('date_detected', [$startDate, $endDate]);
        if ($municipal) {
            $query->where('area', $municipal);
        }
    };

    // ── Cases by severity (filtered) ────────────────────────────────────────
    $casesBySeverity = PestAndDisease::selectRaw('severity, COUNT(*) as count')
        ->where('validation_status', 'approved')
        ->tap($scopePD)
        ->groupBy('severity')
        ->pluck('count', 'severity');

    // ── Validation status (filtered by date) ────────────────────────────────
    $validationStatus = PestAndDisease::selectRaw('validation_status, COUNT(*) as count')
        ->tap($scopePD)
        ->groupBy('validation_status')
        ->pluck('count', 'validation_status');

    // ── Top 10 pests & diseases (filtered) ──────────────────────────────────
    $topPests = PestAndDisease::selectRaw('pest, COUNT(*) as count')
        ->where('validation_status', 'approved')
        ->tap($scopePD)
        ->groupBy('pest')
        ->orderByDesc('count')
        ->limit(10)
        ->get();

    // ── Monthly trend – last 12 months ──────────────────────────────────────
    $months = collect(range(11, 0))->map(fn ($i) => now()->subMonths($i));
    $monthlyTrend = $months->map(function ($m) use ($municipal) {
        $q = PestAndDisease::whereYear('date_detected', $m->year)
                ->whereMonth('date_detected', $m->month)
                ->where('validation_status', 'approved');
        if ($municipal) {
            $q->where('area', $municipal);
        }
        return ['month' => $m->format('M Y'), 'count' => $q->count()];
    });

    // ── Farms by municipality ────────────────────────────────────────────────
    $farmsByMunicipality = Farm::selectRaw('farmer_address_mun, COUNT(*) as count')
        ->whereNotNull('farmer_address_mun')
        ->where('farmer_address_mun', '!=', '')
        ->groupBy('farmer_address_mun')
        ->orderByDesc('count')
        ->limit(10)
        ->get();

    // ── Soil pH distribution ─────────────────────────────────────────────────
    $soilPhDistribution = SoilAnalysis::select(
        DB::raw('CASE
            WHEN ph_level < 5.5 THEN "Very Acidic (< 5.5)"
            WHEN ph_level >= 5.5 AND ph_level < 6.0 THEN "Acidic (5.5–6.0)"
            WHEN ph_level >= 6.0 AND ph_level < 6.5 THEN "Slightly Acidic (6.0–6.5)"
            WHEN ph_level >= 6.5 AND ph_level < 7.0 THEN "Neutral (6.5–7.0)"
            ELSE "Alkaline (> 7.0)"
        END as ph_range'),
        DB::raw('COUNT(*) as count')
    )->whereNotNull('ph_level')
     ->groupBy('ph_range')
     ->get();

    return view('filament.pages.dashboard-print-report', compact(
        'startDate', 'endDate', 'municipal',
        'totalFarmers', 'totalFarms', 'totalArea',
        'totalCases', 'approvedCases', 'criticalCases',
        'totalSoilTests', 'avgPh',
        'casesBySeverity', 'validationStatus',
        'topPests', 'monthlyTrend',
        'farmsByMunicipality', 'soilPhDistribution'
    ));

})->middleware(['web', 'auth'])->name('dashboard.print-report');
