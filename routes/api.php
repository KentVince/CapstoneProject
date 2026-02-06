<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\PestAndDiseaseController;
use App\Http\Controllers\MobileController;
// use App\Http\Controllers\BulletinController;
use App\Http\Controllers\Api\Mobile\BulletinController;
use App\Http\Controllers\SoilAnalysisController;
use App\Models\Barangay;
use App\Models\Farmer;
use App\Models\Farm;
use App\Models\MobileUser;
use App\Models\Bulletin;
use App\Models\SoilAnalysis;
use App\Models\SoilInformation;



/*
|--------------------------------------------------------------------------
| API Routes for CAFARM System
|--------------------------------------------------------------------------
| Handles: 
| - Pest and Disease detection (uploads & indexing)
| - CAFARM mobile app login (farmer verification & authentication)
|--------------------------------------------------------------------------
*/

// 🔒 Sanctum-protected route example
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::get('/mobile/announcements', [BulletinController::class, 'index']);
Route::get('/mobile/announcements', [BulletinController::class, 'index']);

Route::get('/mobile/login-test', function () {
    $user = \App\Models\MobileUser::where('username', 'gomez4')->first();
    $farmer = \App\Models\Farmer::with('farm')->where('id', $user->farmer_id)->first();

    return response()->json([
        'user' => [
            'id' => $user->id,
            'username' => $user->username,
            'type' => $user->type,
            'farmer_id' => $user->farmer_id,
        ],
        'farmer' => [
            'app_no' => $farmer->app_no,
            'farm' => [
                'name' => optional($farmer->farm)->name,
            ],
        ],
        
    ]);
});

// 🐛 1. Pest & Disease Detection (Image Uploads & Validation)
Route::post('/detections', [PestAndDiseaseController::class, 'store']);
Route::get('/detections', [PestAndDiseaseController::class, 'index']);
Route::get('/detections/{id}', [PestAndDiseaseController::class, 'show']);
Route::post('/detections/check-status', [PestAndDiseaseController::class, 'checkValidationStatus']);
Route::post('/detections/by-app-no', [PestAndDiseaseController::class, 'getByAppNo']);


// 🧪 2. Test Upload Endpoint (For mobile image testing)
Route::post('/test-upload', function (Request $request) {
    try {
        if ($request->hasFile('image_url')) {
            $path = $request->file('image_url')->store('pests', 'public');

            return response()->json([
                'message' => '✅ Image uploaded successfully',
                'stored_path' => '/storage/' . $path,
            ], 201);
        }

        return response()->json([
            'error' => '⚠️ No image file detected',
            'inputs' => $request->all(),
            'files' => $request->files->keys(),
        ], 400);

    } catch (\Exception $e) {
        return response()->json([
            'error' => '❌ Upload failed',
            'details' => $e->getMessage(),
        ], 500);
    }
});




Route::get('/mobile/announcements', function () {
    return response()->json([
        'success' => true,
        'data' => Bulletin::orderBy('created_at', 'desc')
            ->where('notification_sent', true) // only published ones
            ->get(['bulletin_id as id', 'category', 'title', 'content', 'date_posted']),
    ]);
});

// 📱 ========================
// CAFARM MOBILE APP ROUTES
// ========================

// Step 1️⃣: Verify App Number (farmer registration check)
Route::post('/mobile/check-app-no', function (Request $request) {
    $request->validate(['app_no' => 'required']);

    $farmer = Farmer::where('app_no', $request->app_no)->first();
    $farm_name = Farm::where('farmer_id',  $farmer->id)->first();

     $farm_barangay = Barangay::where('code', $farmer->barangay)->first();


    if (!$farmer) {
        return response()->json(['message' => '❌ Application number not found'], 404);
    }

    // 🔍 Try to find a linked mobile user
    $mobileUser = MobileUser::where('farmer_id', $farmer->id)->first();

    // 🪄 If no mobile user yet, create one automatically
    if (!$mobileUser) {
        $mobileUser = MobileUser::create([
            'farmer_id' => $farmer->id,
            'username' => strtolower(str_replace(' ', '', $farmer->lastname)) . $farmer->id, // e.g. santos2
            'password' => Hash::make('cafarm123'), // default password
            'app_no' => $farmer->app_no,
            'farm_name' => $farm_name->name,
            'type' => 'Farmer',
             'barangay' => $farm_barangay->barangay,
        ]);
    }

        return response()->json([
            'message' => '✅ App number verified successfully',
            'user' => [
                'username' => $mobileUser->username ?? 'Unknown',
                'type' => $mobileUser->type ?? 'Farmer',
            ],
            'farmer' => [
                'id' => $farmer->id,
                'app_no' => $farmer->app_no ?? '',
                'name' => trim("{$farmer->lastname}, {$farmer->firstname} {$farmer->middlename}"),
                'barangay' => $farm_barangay->barangay ?? '',
                'farm' => [ // ✅ nested farm info inside farmer
                    'name' => $farm_name->name ?? 'Unknown Farm',
                ],
            ],
        ]);

});


// Step 1B️⃣: Verify Username (alternative login method)
Route::post('/mobile/check-username', function (Request $request) {
    $request->validate(['username' => 'required']);

    // Find mobile user by username
    $mobileUser = MobileUser::where('username', $request->username)->first();

    if (!$mobileUser) {
        return response()->json(['message' => '❌ Username not found'], 404);
    }

    // Get farmer details
    $farmer = Farmer::where('id', $mobileUser->farmer_id)->first();

    if (!$farmer) {
        return response()->json(['message' => '❌ Farmer record not found'], 404);
    }

    $farm_name = Farm::where('farmer_id', $farmer->id)->first();
    $farm_barangay = Barangay::where('code', $farmer->barangay)->first();

    return response()->json([
        'message' => '✅ Username verified successfully',
        'user' => [
            'username' => $mobileUser->username,
            'type' => $mobileUser->type ?? 'Farmer',
        ],
        'farmer' => [
            'id' => $farmer->id,
            'app_no' => $farmer->app_no ?? '',
            'name' => trim("{$farmer->lastname}, {$farmer->firstname} {$farmer->middlename}"),
            'barangay' => $farm_barangay->barangay ?? '',
            'farm' => [
                'name' => $farm_name->name ?? 'Unknown Farm',
            ],
        ],
    ]);
});


// Step 2️⃣: Login Authentication
Route::post('/mobile/login', function (Request $request) {
    $request->validate([
        'username' => 'required',
        'password' => 'required',
    ]);

    $user = MobileUser::where('username', $request->username)->first();
    if (!$user) {
        return response()->json(['message' => '❌ Username not found'], 404);
    }

    if (!Hash::check($request->password, $user->password)) {
        return response()->json(['message' => '⚠️ Incorrect password'], 401);
    }

    $farmer = Farmer::with('farm')->find($user->farmer_id);

    return response()->json([
        'message' => '✅ Login successful',
        'user' => [
            'id' => $user->id,
            'username' => $user->username,
            'type' => $user->type,
            'app_no' => $user->app_no,
        ],
        'farmer' => [
            'id' => $farmer->id,
            'app_no' => $farmer->app_no,
            'name' => trim("{$farmer->lastname}, {$farmer->firstname} {$farmer->middlename}"),
            'barangay' => $farmer->barangay,
            'farm' => [
                'id' => optional($farmer->farm)->id ?? '',
                'name' => optional($farmer->farm)->name ?? 'N/A',
            ],
        ],
    ]);
});


Route::post('/mobile/soil-sync', function (Request $request) {
    // 1️⃣ Create or find soil information record
    SoilAnalysis::create([
        'farmer_id'     => $request->farmer_id,
        'farm_id'       => $request->farm_id,
        'farm_name'     => $request->farm_name ?? 'Unknown',
        'crop_variety'  => $request->crop_variety ?? 'Coffee',
        'soil_type'     => $request->soil_type ?? null,
        'date_collected'=> $request->date_collected ?? now(),
        'location'      => $request->location ?? 'Unknown',
         'ref_no'         => $request->ref_no,
        'submitted_by'   => $request->submitted_by ?? 'Mobile App User',
        'date_submitted' => $request->date_submitted,
        'date_analyzed'  => $request->date_analyzed,
        'lab_no'         => $request->lab_no ?? 'LAB-' . rand(100, 999),
        'field_no'       => $request->field_no ?? 'FIELD-' . rand(10, 99),
        'ph_level'       => $request->ph_level,
        'nitrogen'       => $request->nitrogen,
        'phosphorus'     => $request->phosphorus,
        'potassium'      => $request->potassium,
        'organic_matter' => $request->organic_matter,
        'recommendation' => $request->recommendation,
    ]);



    return response()->json([
        'message' => '✅ Soil information and analysis synced successfully!',
    ]);
});

Route::post('/mobile/soil/analysis', [SoilAnalysisController::class, 'store']);

// Route::post('/mobile/soil/analysis', function (Request $request) {
//     $data = $request->validate([
//         'farmer_id'      => 'required|integer',
//         'farm_id'        => 'nullable|integer',
//         'farm_name'      => 'nullable|string',
//         'crop_variety'   => 'nullable|string',
//         'soil_type'      => 'nullable|string',
//         'date_collected' => 'nullable|date',
//         'location'       => 'nullable|string',
//         'ref_no'         => 'nullable|string',
//         'submitted_by'   => 'nullable|string',
//         'date_submitted' => 'nullable|string',
//         'date_analyzed'  => 'nullable|string',
//         'lab_no'         => 'nullable|string',
//         'field_no'       => 'nullable|string',
//         'ph_level'       => 'nullable|numeric',
//         'nitrogen'       => 'nullable|numeric',
//         'phosphorus'     => 'nullable|numeric',
//         'potassium'      => 'nullable|numeric',
//         'organic_matter' => 'nullable|numeric',
//         'recommendation' => 'nullable|string',
//     ]);

//     // ✅ Use SoilAnalysis model instead of SoilInformation
//     $analysis = SoilAnalysis::create($data);

//     return response()->json([
//         'message' => '✅ Soil analysis saved successfully',
//         'soil_analysis_id' => $analysis->id,
//     ]);
// });



// 🌱 CAFARM Mobile Sync API
Route::post('/mobile/soil/analysis', [SoilAnalysisController::class, 'store']);
Route::get('/mobile/soil/analysis', [SoilAnalysisController::class, 'index']); 


// Add this route with your other mobile routes
Route::post('/mobile/change-password', [MobileController::class, 'changePassword']);

// FCM Token Update - for push notifications
Route::post('/mobile/update-fcm-token', function (Request $request) {
    $request->validate([
        'app_no' => 'required|string',
        'fcm_token' => 'required|string',
    ]);

    $updated = MobileUser::where('app_no', $request->app_no)
        ->update(['fcm_token' => $request->fcm_token]);

    if ($updated) {
        return response()->json([
            'success' => true,
            'message' => 'FCM token updated successfully',
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'User not found',
    ], 404);
});