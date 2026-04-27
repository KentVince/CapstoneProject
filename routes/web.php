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

// Lightweight endpoint for sidebar badge polling (unviewed by current user)
Route::get('/admin/api/pending-detections-count', function () {
    $userId = auth()->id();
    $count = PestAndDisease::where('validation_status', 'pending')
        ->whereNotExists(function ($q) use ($userId) {
            $q->from('admin_record_views')
                ->whereColumn('record_id', 'pest_and_disease.case_id')
                ->where('record_type', 'pest_disease')
                ->where('user_id', $userId);
        })
        ->count();
    return response()->json(['count' => $count]);
})->middleware(['web', 'auth'])->name('admin.pending-count');

// Lightweight endpoint for Soil Analysis sidebar badge polling (unviewed by current user)
Route::get('/admin/api/pending-soil-count', function () {
    $userId = auth()->id();
    $count = SoilAnalysis::where('validation_status', 'pending')
        ->whereNotExists(function ($q) use ($userId) {
            $q->from('admin_record_views')
                ->whereColumn('record_id', 'soil_analysis.id')
                ->where('record_type', 'soil_analysis')
                ->where('user_id', $userId);
        })
        ->count();
    return response()->json(['count' => $count]);
})->middleware(['web', 'auth'])->name('admin.pending-soil-count');

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

    $municipal    = $request->get('municipal', 'Maragusan');
    $barangayCode = $request->get('barangay');
    $barangayName = $barangayCode
        ? \App\Models\Barangay::where('code', $barangayCode)->value('barangay')
        : null;

    // Resolve all Maragusan barangay codes for broad scope when no specific barangay chosen
    $munCode        = \App\Models\Municipality::where('municipality', $municipal)->value('code');
    $maraguanBgyCodes = \App\Models\Barangay::where('muni_filter', $munCode)->pluck('code')->toArray();
    $maraguanBgyNames = \App\Models\Barangay::where('muni_filter', $munCode)->pluck('barangay')->toArray();

    // ── Helper: apply barangay/municipality scope to a P&D query ────────────
    $applyScope = function ($query) use ($barangayName, $maraguanBgyNames) {
        if ($barangayName) {
            $query->where(function ($q) use ($barangayName) {
                $q->where('area', 'LIKE', $barangayName . ',%')
                  ->orWhere('area', $barangayName);
            });
        } else {
            // Scope to Maragusan — match any of its barangay names
            $query->where(function ($q) use ($maraguanBgyNames) {
                foreach ($maraguanBgyNames as $name) {
                    $q->orWhere('area', 'LIKE', $name . ',%')
                      ->orWhere('area', $name);
                }
            });
        }
    };

    // ── Summary (scoped) ────────────────────────────────────────────────────
    $totalCases    = PestAndDisease::tap($applyScope)->count();
    $approvedCases = PestAndDisease::where('validation_status', 'approved')->tap($applyScope)->count();
    $pendingCases  = PestAndDisease::where('validation_status', 'pending')->tap($applyScope)->count();
    $rejectedCases = PestAndDisease::where('validation_status', 'rejected')->tap($applyScope)->count();

    $low    = (int) PestAndDisease::where('validation_status', 'approved')->where('severity', 'low')->tap($applyScope)->count();
    $medium = (int) PestAndDisease::where('validation_status', 'approved')->where('severity', 'medium')->tap($applyScope)->count();
    $high   = (int) PestAndDisease::where('validation_status', 'approved')->where('severity', 'high')->tap($applyScope)->count();

    // ── Per-barangay pest distribution (with purok via farmer join) ────────
    $rawRows = PestAndDisease::select(
            'pest_and_disease.area',
            'pest_and_disease.pest',
            'pest_and_disease.severity',
            DB::raw('COALESCE(farmers.farmer_address_prk, "—") as purok'),
            DB::raw('COUNT(*) as case_count'),
            DB::raw('MAX(pest_and_disease.date_detected) as last_detected')
        )
        ->leftJoin('farmers', 'farmers.id', '=', 'pest_and_disease.farmer_id')
        ->where('pest_and_disease.validation_status', 'approved')
        ->tap($applyScope)
        ->groupBy('pest_and_disease.area', 'pest_and_disease.pest', 'pest_and_disease.severity', 'farmers.farmer_address_prk')
        ->orderBy('pest_and_disease.area')
        ->orderBy('farmers.farmer_address_prk')
        ->orderByDesc('case_count')
        ->get();

    // Group rows: barangay (first part of area) → collection of rows
    $byBarangay = $rawRows->groupBy(fn ($r) => trim(explode(',', $r->area)[0]));

    // ── Top pests overall (scoped) ───────────────────────────────────────────
    $topPests = PestAndDisease::selectRaw('pest, COUNT(*) as count')
        ->where('validation_status', 'approved')
        ->tap($applyScope)
        ->groupBy('pest')
        ->orderByDesc('count')
        ->limit(10)
        ->get();

    return view('filament.pages.dashboard-print-report', compact(
        'municipal', 'barangayName',
        'totalCases', 'approvedCases', 'pendingCases', 'rejectedCases',
        'low', 'medium', 'high',
        'byBarangay', 'topPests'
    ));

})->middleware(['web', 'auth'])->name('dashboard.print-report');


// ─── Soil Analysis Print Report ──────────────────────────────────────────────
Route::get('/admin/soil-report', function (Request $request) {

    $municipal    = $request->get('municipal', 'Maragusan');
    $barangayCode = $request->get('barangay');
    $barangayName = $barangayCode
        ? \App\Models\Barangay::where('code', $barangayCode)->value('barangay')
        : null;

    // Resolve Maragusan barangay codes for scoping
    $munCode       = \App\Models\Municipality::where('municipality', $municipal)->value('code');
    $maragusanCodes = \App\Models\Barangay::where('muni_filter', $munCode)->pluck('code')->toArray();

    // Base query scope: filter farms by barangay/municipality
    $farmScope = function ($q) use ($barangayCode, $maragusanCodes) {
        if ($barangayCode) {
            $q->where('farmer_address_bgy', $barangayCode);
        } else {
            $q->whereIn('farmer_address_bgy', $maragusanCodes);
        }
    };

    // ── Summary ─────────────────────────────────────────────────────────────
    $totalFarmsInScope   = Farm::tap($farmScope)->count();
    $farmsWithLabData    = Farm::tap($farmScope)
        ->whereHas('soilAnalyses', fn ($q) => $q->whereNotNull('lab_no')->where('lab_no', '!=', ''))
        ->count();
    $farmsWithSoilRecord = Farm::tap($farmScope)->whereHas('soilAnalyses')->count();
    $totalSoilTests      = SoilAnalysis::whereHas('farm', $farmScope)->count();
    $approvedTests       = SoilAnalysis::where('validation_status', 'approved')->whereHas('farm', $farmScope)->count();
    $pendingTests        = SoilAnalysis::where('validation_status', 'pending')->whereHas('farm', $farmScope)->count();

    // ── Average nutrient levels (approved) ──────────────────────────────────
    $avgBase = SoilAnalysis::where('validation_status', 'approved')->whereHas('farm', $farmScope);
    $avgPh   = round((float) (clone $avgBase)->avg('ph_level'), 2);
    $avgN    = round((float) (clone $avgBase)->avg('nitrogen'), 4);
    $avgP    = round((float) (clone $avgBase)->avg('phosphorus'), 2);
    $avgK    = round((float) (clone $avgBase)->avg('potassium'), 4);
    $avgOm   = round((float) (clone $avgBase)->avg('organic_matter'), 2);

    // ── pH distribution ──────────────────────────────────────────────────────
    $phDistribution = SoilAnalysis::select(
            DB::raw('CASE
                WHEN ph_level < 5.5 THEN "Very Acidic (< 5.5)"
                WHEN ph_level >= 5.5 AND ph_level < 6.0 THEN "Acidic (5.5–6.0)"
                WHEN ph_level >= 6.0 AND ph_level < 6.5 THEN "Slightly Acidic (6.0–6.5)"
                WHEN ph_level >= 6.5 AND ph_level < 7.0 THEN "Neutral (6.5–7.0)"
                ELSE "Alkaline (> 7.0)"
            END as ph_range'),
            DB::raw('COUNT(*) as count')
        )
        ->whereNotNull('ph_level')
        ->where('validation_status', 'approved')
        ->whereHas('farm', $farmScope)
        ->groupBy('ph_range')
        ->get();

    // ── Per-barangay nutrient averages ───────────────────────────────────────
    $nutrientByBarangay = SoilAnalysis::select(
            'barangays.barangay as barangay_name',
            DB::raw('COUNT(soil_analysis.id) as sample_count'),
            DB::raw('ROUND(AVG(soil_analysis.ph_level), 2) as avg_ph'),
            DB::raw('ROUND(AVG(soil_analysis.nitrogen), 4) as avg_n'),
            DB::raw('ROUND(AVG(soil_analysis.phosphorus), 2) as avg_p'),
            DB::raw('ROUND(AVG(soil_analysis.potassium), 4) as avg_k'),
            DB::raw('ROUND(AVG(soil_analysis.organic_matter), 2) as avg_om')
        )
        ->join('farms', 'farms.id', '=', 'soil_analysis.farm_id')
        ->join('barangays', 'barangays.code', '=', 'farms.farmer_address_bgy')
        ->where('soil_analysis.validation_status', 'approved')
        ->tap(function ($q) use ($barangayCode, $maragusanCodes) {
            if ($barangayCode) {
                $q->where('farms.farmer_address_bgy', $barangayCode);
            } else {
                $q->whereIn('farms.farmer_address_bgy', $maragusanCodes);
            }
        })
        ->groupBy('barangays.barangay')
        ->orderBy('barangays.barangay')
        ->get();

    // ── Lab records table ────────────────────────────────────────────────────
    $labRecords = SoilAnalysis::select(
            'soil_analysis.sample_id',
            'soil_analysis.farm_name',
            'soil_analysis.lab_no',
            'soil_analysis.date_collected',
            'soil_analysis.date_analyzed',
            'soil_analysis.ph_level',
            'soil_analysis.nitrogen',
            'soil_analysis.phosphorus',
            'soil_analysis.potassium',
            'soil_analysis.organic_matter',
            'soil_analysis.crop_variety',
            'soil_analysis.validation_status',
            'barangays.barangay as barangay_name',
            'farmers.farmer_address_prk as purok'
        )
        ->join('farms', 'farms.id', '=', 'soil_analysis.farm_id')
        ->join('barangays', 'barangays.code', '=', 'farms.farmer_address_bgy')
        ->leftJoin('farmers', 'farmers.id', '=', 'soil_analysis.farmer_id')
        ->tap(function ($q) use ($barangayCode, $maragusanCodes) {
            if ($barangayCode) {
                $q->where('farms.farmer_address_bgy', $barangayCode);
            } else {
                $q->whereIn('farms.farmer_address_bgy', $maragusanCodes);
            }
        })
        ->orderBy('barangays.barangay')
        ->orderBy('soil_analysis.date_collected', 'desc')
        ->get();

    return view('filament.pages.soil-print-report', compact(
        'municipal', 'barangayName',
        'totalFarmsInScope', 'farmsWithLabData', 'farmsWithSoilRecord',
        'totalSoilTests', 'approvedTests', 'pendingTests',
        'avgPh', 'avgN', 'avgP', 'avgK', 'avgOm',
        'phDistribution', 'nutrientByBarangay', 'labRecords'
    ));

})->middleware(['web', 'auth'])->name('dashboard.soil-report');


// ─── Farmer & Farm List Report ───────────────────────────────────────────────
Route::get('/admin/farmer-farm-report', function (Request $request) {

    $municipal    = $request->get('municipal', 'Maragusan');
    $barangayCode = $request->get('barangay');
    $barangayName = $barangayCode
        ? \App\Models\Barangay::where('code', $barangayCode)->value('barangay')
        : null;

    $munCode        = \App\Models\Municipality::where('municipality', $municipal)->value('code');
    $maragusanCodes = \App\Models\Barangay::where('muni_filter', $munCode)->pluck('code')->toArray();

    $farmerScope = function ($q) use ($barangayCode, $maragusanCodes) {
        if ($barangayCode) {
            $q->where('farmer_address_bgy', $barangayCode);
        } else {
            $q->whereIn('farmer_address_bgy', $maragusanCodes);
        }
    };

    // Farmers (with their farms) within scope
    $farmers = Farmer::with(['farms', 'barangayData'])
        ->tap($farmerScope)
        ->orderBy('last_name')
        ->orderBy('first_name')
        ->get();

    // Summary stats
    $totalFarmers = $farmers->count();
    $totalFarms   = $farmers->sum(fn ($f) => $f->farms->count());
    $totalArea    = $farmers->flatMap->farms->sum(fn ($farm) => (float) ($farm->crop_area ?? 0));
    $verifiedFarms = $farmers->flatMap->farms
        ->filter(fn ($farm) => strtolower((string) $farm->verified_area) === 'yes')
        ->count();

    return view('filament.pages.farmer-farm-print-report', compact(
        'municipal', 'barangayName',
        'farmers', 'totalFarmers', 'totalFarms', 'totalArea', 'verifiedFarms'
    ));

})->middleware(['web', 'auth'])->name('dashboard.farmer-farm-report');


// ─── Farmer Details Print ────────────────────────────────────────────────────
Route::get('/admin/farmers/{farmer}/print', function (Farmer $farmer) {
    $farmer->load(['farm', 'barangayData', 'municipalityData']);

    return view('farmers.print', compact('farmer'));
})->middleware(['web', 'auth'])->name('farmers.print');
