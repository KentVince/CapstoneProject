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
use App\Models\AgriculturalProfessional;
use App\Models\Barangay;
use App\Models\Farmer;
use App\Models\Farm;
use App\Models\MobileUser;
use App\Models\Municipality;
use App\Models\Bulletin;
use App\Models\SoilAnalysis;
use App\Models\SoilInformation;
use App\Models\User;
use Filament\Notifications\Notification;



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
                'latitude' => optional($farmer->farm)->latitude ?? null,
                'longitude' => optional($farmer->farm)->longitude ?? null,
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
Route::post('/detections/farmer-action', [PestAndDiseaseController::class, 'saveFarmerAction']);
Route::post('/detections/{id}/expert-comments', [PestAndDiseaseController::class, 'addExpertComment']);
Route::post('/detections/nearby-severe', [PestAndDiseaseController::class, 'getNearbySevere']);


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
    $items = Bulletin::orderBy('created_at', 'desc')
        ->where('notification_sent', true)
        ->get(['bulletin_id', 'category', 'title', 'content', 'date_posted', 'attachments'])
        ->map(function ($item) {
            $attachments = collect($item->attachments ?? [])
                ->map(fn($path) => [
                    'url'      => url('storage/' . $path),
                    'path'     => $path,
                    'is_image' => preg_match('/\.(jpg|jpeg|png|webp|gif)$/i', $path) === 1,
                ])->values()->all();
            return [
                'id'          => $item->bulletin_id,
                'category'    => $item->category,
                'title'       => $item->title,
                'content'     => $item->content,
                'date_posted' => $item->date_posted,
                'attachments' => $attachments,
            ];
        });
    return response()->json(['success' => true, 'data' => $items]);
});

// 📱 ========================
// CAFARM MOBILE APP ROUTES
// ========================

// Step 1: Verify App Number (farmer or expert)
Route::post('/mobile/check-app-no', function (Request $request) {
    $request->validate(['app_no' => 'required']);

    // Check if it's an expert (AP-prefix) or farmer
    $professional = AgriculturalProfessional::where('app_no', $request->app_no)->first();

    if ($professional) {
        // Expert flow
        $mobileUser = MobileUser::where('professional_id', $professional->id)->first();

        if (!$mobileUser) {
            $mobileUser = MobileUser::create([
                'professional_id' => $professional->id,
                'username' => strtolower(str_replace(' ', '', $professional->lastname)) . $professional->id,
                'password' => Hash::make('cafarm123'),
                'app_no' => $professional->app_no,
                'type' => 'agricultural_professional',
                'barangay' => $professional->barangay,
                'contact_no' => $professional->phone_no,
                'email' => $professional->email_add,
            ]);
        }

        $barangayName = Barangay::where('code', $professional->barangay)->value('barangay') ?? $professional->barangay;
        $municipalityName = Municipality::where('code', $professional->municipality)->value('municipality') ?? $professional->municipality;

        return response()->json([
            'message' => 'App number verified successfully',
            'user_type' => 'expert',
            'user' => [
                'username' => $mobileUser->username,
                'type' => 'agricultural_professional',
            ],
            'expert' => [
                'id' => $professional->id,
                'app_no' => $professional->app_no,
                'name' => trim("{$professional->lastname}, {$professional->firstname} {$professional->middlename}"),
                'agency' => $professional->agency,
                'municipality' => $municipalityName,
                'municipality_code' => $professional->municipality,
                'barangay' => $barangayName,
                'phone_no' => $professional->phone_no,
                'email' => $professional->email_add,
            ],
        ]);
    }

    // Farmer flow
    $farmer = Farmer::where('app_no', $request->app_no)->first();

    if (!$farmer) {
        return response()->json(['message' => 'Application number not found'], 404);
    }

    $farm = Farm::where('farmer_id', $farmer->id)->first();
    $farm_barangay = Barangay::where('code', $farmer->farmer_address_bgy)->first();

    $mobileUser = MobileUser::where('farmer_id', $farmer->id)->first();

    if (!$mobileUser) {
        $mobileUser = MobileUser::create([
            'farmer_id' => $farmer->id,
            'username' => strtolower(str_replace(' ', '', $farmer->last_name)) . $farmer->id,
            'password' => Hash::make('cafarm123'),
            'app_no' => $farmer->app_no,
            'farm_name' => $farm->farm_name ?? 'Unknown Farm',
            'type' => 'farmer',
            'barangay' => $farm_barangay->barangay ?? '',
        ]);
    }

    return response()->json([
        'message' => 'App number verified successfully',
        'user_type' => 'farmer',
        'user' => [
            'username' => $mobileUser->username ?? 'Unknown',
            'type' => $mobileUser->type ?? 'farmer',
        ],
        'farmer' => [
            'id' => $farmer->id,
            'app_no' => $farmer->app_no ?? '',
            'name' => trim("{$farmer->last_name}, {$farmer->first_name} {$farmer->middle_name}"),
            'last_name' => $farmer->last_name ?? '',
            'first_name' => $farmer->first_name ?? '',
            'barangay' => $farm_barangay->barangay ?? '',
            'farm' => [
                'id' => $farm->id ?? '',
                'farm_name' => $farm->farm_name ?? 'Unknown Farm',
                'latitude' => $farm->latitude ?? null,
                'longitude' => $farm->longtitude ?? null,
                'verified_area' => $farm->verified_area ?? null,
                'status' => $farm->status ?? null,
            ],
        ],
    ]);
});


// Step 1B: Verify Username (alternative login method - farmer or expert)
Route::post('/mobile/check-username', function (Request $request) {
    $request->validate(['username' => 'required']);

    $mobileUser = MobileUser::where('username', $request->username)->first();

    if (!$mobileUser) {
        return response()->json(['message' => 'Username not found'], 404);
    }

    // Expert flow
    if ($mobileUser->professional_id) {
        $professional = AgriculturalProfessional::find($mobileUser->professional_id);

        if (!$professional) {
            return response()->json(['message' => 'Professional record not found'], 404);
        }

        $barangayName = Barangay::where('code', $professional->barangay)->value('barangay') ?? $professional->barangay;
        $municipalityName = Municipality::where('code', $professional->municipality)->value('municipality') ?? $professional->municipality;

        return response()->json([
            'message' => 'Username verified successfully',
            'user_type' => 'expert',
            'user' => [
                'username' => $mobileUser->username,
                'type' => 'agricultural_professional',
            ],
            'expert' => [
                'id' => $professional->id,
                'app_no' => $professional->app_no,
                'name' => trim("{$professional->lastname}, {$professional->firstname} {$professional->middlename}"),
                'agency' => $professional->agency,
                'municipality' => $municipalityName,
                'municipality_code' => $professional->municipality,
                'barangay' => $barangayName,
                'phone_no' => $professional->phone_no,
                'email' => $professional->email_add,
            ],
        ]);
    }

    // Farmer flow
    $farmer = Farmer::where('id', $mobileUser->farmer_id)->first();

    if (!$farmer) {
        return response()->json(['message' => 'Farmer record not found'], 404);
    }

    $farm = Farm::where('farmer_id', $farmer->id)->first();
    $farm_barangay = Barangay::where('code', $farmer->farmer_address_bgy)->first();

    return response()->json([
        'message' => 'Username verified successfully',
        'user_type' => 'farmer',
        'user' => [
            'username' => $mobileUser->username,
            'type' => $mobileUser->type ?? 'farmer',
        ],
        'farmer' => [
            'id' => $farmer->id,
            'app_no' => $farmer->app_no ?? '',
            'name' => trim("{$farmer->last_name}, {$farmer->first_name} {$farmer->middle_name}"),
            'last_name' => $farmer->last_name ?? '',
            'first_name' => $farmer->first_name ?? '',
            'barangay' => $farm_barangay->barangay ?? '',
            'farm' => [
                'id' => $farm->id ?? '',
                'farm_name' => $farm->farm_name ?? 'Unknown Farm',
                'latitude' => $farm->latitude ?? null,
                'longitude' => $farm->longtitude ?? null,
            ],
        ],
    ]);
});


// Step 2: Login Authentication (farmer or expert)
Route::post('/mobile/login', function (Request $request) {
    $request->validate([
        'username' => 'required',
        'password' => 'required',
    ]);

    $user = MobileUser::where('username', $request->username)->first();
    if (!$user) {
        return response()->json(['message' => 'Username not found'], 404);
    }

    if (!Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Incorrect password'], 401);
    }

    // Expert login
    if ($user->professional_id) {
        $professional = AgriculturalProfessional::find($user->professional_id);

        $barangayName = Barangay::where('code', $professional->barangay)->value('barangay') ?? $professional->barangay;
        $municipalityName = Municipality::where('code', $professional->municipality)->value('municipality') ?? $professional->municipality;

        return response()->json([
            'message' => 'Login successful',
            'user_type' => 'expert',
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'type' => 'agricultural_professional',
                'app_no' => $user->app_no,
            ],
            'expert' => [
                'id' => $professional->id,
                'app_no' => $professional->app_no,
                'name' => trim("{$professional->lastname}, {$professional->firstname} {$professional->middlename}"),
                'agency' => $professional->agency,
                'municipality' => $municipalityName,
                'municipality_code' => $professional->municipality,
                'barangay' => $barangayName,
                
                'phone_no' => $professional->phone_no,
                'email' => $professional->email_add,
            ],
        ]);
    }

    // Farmer login
    $farmer = Farmer::with('farm')->find($user->farmer_id);

    // Resolve barangay and municipality codes → human-readable names
    $farmerBrgyName = Barangay::where('code', $farmer->farmer_address_bgy)->value('barangay')
        ?? $farmer->farmer_address_bgy;
    $farmerMunName = Municipality::where('code', $farmer->farmer_address_mun)->value('municipality')
        ?? $farmer->farmer_address_mun;

    $farmBrgyName = optional($farmer->farm)->farmer_address_bgy
        ? (Barangay::where('code', $farmer->farm->farmer_address_bgy)->value('barangay')
            ?? $farmer->farm->farmer_address_bgy)
        : null;
    $farmMunName = optional($farmer->farm)->farmer_address_mun
        ? (Municipality::where('code', $farmer->farm->farmer_address_mun)->value('municipality')
            ?? $farmer->farm->farmer_address_mun)
        : null;

    return response()->json([
        'message' => 'Login successful',
        'user_type' => 'farmer',
        'user' => [
            'id' => $user->id,
            'username' => $user->username,
            'type' => $user->type,
            'app_no' => $user->app_no,
        ],
        'farmer' => [
            'id' => $farmer->id,
            'app_no' => $farmer->app_no,
            'rsbsa_no' => $farmer->rsbsa_no ?? '',
            'name' => trim("{$farmer->last_name}, {$farmer->first_name} {$farmer->middle_name}"),
            'last_name' => $farmer->last_name ?? '',
            'first_name' => $farmer->first_name ?? '',
            'barangay' => $farmerBrgyName,
            'municipality' => $farmerMunName,
            'farm' => [
                'id' => optional($farmer->farm)->id ?? '',
                'farm_name' => optional($farmer->farm)->farm_name ?? 'N/A',
                'crop_area' => optional($farmer->farm)->crop_area ?? '',
                'latitude' => optional($farmer->farm)->latitude ?? null,
                'longitude' => optional($farmer->farm)->longtitude ?? null,
                'barangay' => $farmBrgyName,
                'municipality' => $farmMunName,
            ],
        ],
    ]);
});


// Expert: Get farms list filtered by agency scope
Route::post('/mobile/expert/farms', function (Request $request) {
    $request->validate([
        'expert_id' => 'required|integer',
    ]);

    $professional = AgriculturalProfessional::find($request->expert_id);

    if (!$professional) {
        return response()->json(['message' => 'Expert not found'], 404);
    }

    $agency = strtoupper(trim($professional->agency ?? ''));

    // MAGSO = Municipal Agriculture Office → only farms in the expert's municipality
    // PAGRO / DDOSC / others = Provincial level → all farms
    if (str_contains($agency, 'MAGSO')) {
        $farms = Farm::where('farmer_address_mun', $professional->municipality)
            ->with('farmer:id,app_no,first_name,last_name,middle_name')
            ->get();
    } else {
        $farms = Farm::with('farmer:id,app_no,first_name,last_name,middle_name')->get();
    }

    $result = $farms->map(function ($farm) {
        $farmer = $farm->farmer;
        $barangayName = Barangay::where('code', $farm->farmer_address_bgy)->value('barangay') ?? $farm->farmer_address_bgy;
        $municipalityName = Municipality::where('code', $farm->farmer_address_mun)->value('municipality') ?? $farm->farmer_address_mun;

        return [
            'id' => $farm->id,
            'farm_id' => $farm->id,
            'farm_name' => $farm->farm_name,
            'farmer_id' => $farmer->id ?? null,
            'app_no' => $farmer->app_no ?? '',
            'farmer_name' => $farmer
                ? trim("{$farmer->last_name}, {$farmer->first_name} {$farmer->middle_name}")
                : 'Unknown',
            'barangay' => $barangayName,
            'municipality' => $municipalityName,
            'crop_area' => $farm->crop_area,
            'latitude' => $farm->latitude,
            'longitude' => $farm->longtitude,
        ];
    });

    return response()->json([
        'message' => 'Farms retrieved successfully',
        'agency' => $professional->agency,
        'scope' => str_contains($agency, 'MAGSO') ? 'municipal' : 'provincial',
        'count' => $result->count(),
        'data' => $result,
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
        'analysis_type' => $request->analysis_type ?? null,
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
         'validation_status' => $request->validation_status,
          'expert_comments' => $request->expert_comments,
           'validated_by' => $request->validated_by,
           'validated_at' => $request->validated_at,


    ]);

    // Notification is handled by SoilAnalysisObserver::created()

    return response()->json([
        'message' => 'Soil information and analysis synced successfully!',
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

// Soil Analysis - Get by app_no (for mobile app refresh with expert recommendations)
Route::post('/soil-analysis/by-app-no', [SoilAnalysisController::class, 'getByAppNo']);

// Soil Analysis - Farmer reply to expert recommendation
Route::post('/soil-analysis/farmer-reply', [SoilAnalysisController::class, 'farmerReply']);

// Soil Analysis Conversations
Route::get('/soil-analysis/{id}/conversations', [SoilAnalysisController::class, 'getConversations']);
Route::post('/soil-analysis/{id}/conversations', [SoilAnalysisController::class, 'storeConversation']);

// Soil Analysis - Expert adds additional comment (from Flutter)
Route::post('/soil-analysis/{id}/expert-comments', [SoilAnalysisController::class, 'addExpertComment']);

// Add this route with your other mobile routes
Route::post('/mobile/change-password', [MobileController::class, 'changePassword']);

// Update farm GPS coordinates (and optionally verified_area + status) from mobile device
Route::post('/mobile/update-farm-gps', function (Request $request) {
    $request->validate([
        'farm_id'       => 'required|integer',
        'latitude'      => 'required|numeric',
        'longitude'     => 'required|numeric',
        'verified_area' => 'nullable|string|max:255',
        'status'        => 'nullable|string|max:255',
    ]);

    $farm = Farm::find($request->farm_id);

    if (!$farm) {
        return response()->json(['success' => false, 'message' => 'Farm not found'], 404);
    }

    $updates = [
        'latitude'   => $request->latitude,
        'longtitude' => $request->longitude, // note: column has typo in DB
    ];
    if ($request->filled('verified_area')) {
        $updates['verified_area'] = $request->verified_area;
    }
    if ($request->filled('status')) {
        $updates['status'] = $request->status;
    }

    $farm->update($updates);

    return response()->json([
        'success'       => true,
        'message'       => 'Farm updated successfully',
        'latitude'      => $request->latitude,
        'longitude'     => $request->longitude,
        'verified_area' => $farm->verified_area,
        'status'        => $farm->status,
    ]);
});

// FCM Token Update - for push notifications
Route::post('/mobile/update-fcm-token', function (Request $request) {
    $request->validate([
        'app_no' => 'required|string',
        'fcm_token' => 'required|string',
    ]);

    $exists = MobileUser::where('app_no', $request->app_no)->exists();

    if (!$exists) {
        return response()->json([
            'success' => false,
            'message' => 'User not found',
        ], 404);
    }

    MobileUser::where('app_no', $request->app_no)
        ->update(['fcm_token' => $request->fcm_token]);

    return response()->json([
        'success' => true,
        'message' => 'FCM token updated successfully',
    ]);
});