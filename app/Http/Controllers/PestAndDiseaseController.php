<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Farmer;
use App\Models\PestAndDisease;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class PestAndDiseaseController extends Controller
{
    /**
     * 📥 Store detection from Flutter mobile app
     */
public function store(Request $request)
{
  
    try {
        // Validate required fields only (image handled separately)
        $validated = $request->validate([
            'app_no'        => 'nullable|string',
            'expert_id'     => 'nullable|integer|exists:agricultural_professionals,id',
            'farmer_id'     => 'nullable|integer|exists:farmers,id',
            'farm_id'       => 'nullable|integer|exists:farms,id',
            'pest'          => 'required|string',
            'type'          => 'nullable|string|in:pest,disease',
            'confidence'    => 'required|numeric',
            'latitude'      => 'required|numeric',
            'longitude'     => 'required|numeric',
            'area'          => 'nullable|string',
            'date_detected' => 'nullable|string',
            'severity'      => 'nullable|string',
        ]);

        // Auto-resolve farmer_id and farm_id from app_no if not explicitly provided
        if (empty($validated['farmer_id']) && !empty($validated['app_no'])) {
            $farmer = Farmer::where('app_no', $validated['app_no'])->first();
            if ($farmer) {
                $validated['farmer_id'] = $farmer->id;

                if (empty($validated['farm_id'])) {
                    $farm = $farmer->farm;
                    if ($farm) {
                        $validated['farm_id'] = $farm->id;
                    }
                }
            }
        }

        // Handle image upload separately - don't fail if image is missing or invalid
        $imageWarning = null;
        if ($request->hasFile('image')) {
            try {
                $imageFile = $request->file('image');

                if (!$imageFile->isValid()) {
                    $imageWarning = 'Image file was invalid: ' . $imageFile->getErrorMessage();
                    Log::warning('Image upload skipped - invalid file', [
                        'error' => $imageFile->getErrorMessage(),
                    ]);
                } else {
                    // Check file size (10MB limit to be more lenient)
                    $maxSize = 10 * 1024 * 1024; // 10MB in bytes
                    if ($imageFile->getSize() > $maxSize) {
                        $imageWarning = 'Image too large (max 10MB), skipped';
                        Log::warning('Image upload skipped - file too large', [
                            'size' => $imageFile->getSize(),
                        ]);
                    } else {
                        // Check mime type
                        $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg'];
                        if (!in_array($imageFile->getMimeType(), $allowedMimes)) {
                            $imageWarning = 'Invalid image format, skipped';
                            Log::warning('Image upload skipped - invalid mime type', [
                                'mime' => $imageFile->getMimeType(),
                            ]);
                        } else {
                            // All checks passed, store the image
                            $path = $imageFile->store('detections', 'public');
                            $validated['image_path'] = $path;
                        }
                    }
                }
            } catch (\Exception $imageException) {
                $imageWarning = 'Image upload failed: ' . $imageException->getMessage();
                Log::warning('Image upload exception', [
                    'error' => $imageException->getMessage(),
                ]);
            }
        }

        // Always create the detection record, even without image
        $detection = PestAndDisease::create($validated);

        Log::info('Detection stored successfully', [
            'case_id' => $detection->case_id,
            'has_image' => isset($validated['image_path']),
        ]);

        $response = [
            'message' => 'Detection saved successfully',
            'data' => $detection,
        ];

        if ($imageWarning) {
            $response['image_warning'] = $imageWarning;
        }

        return response()->json($response, 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation failed for detection', [
            'errors' => $e->errors(),
        ]);
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        Log::error('Error storing detection: ' . $e->getMessage());
        return response()->json([
            'message' => 'Error storing detection',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    /**
     * 📋 List all detections with validation status
     */
    public function index(Request $request)
    {
        $query = PestAndDisease::with('validator:id,name')->latest();

        // Optional filter by app_no (for Flutter to get user's detections)
        if ($request->has('app_no')) {
            $query->where('app_no', $request->app_no);
        }

        // Optional filter by validation status
        if ($request->has('status')) {
            $query->where('validation_status', $request->status);
        }

        $detections = $query->get()->map(function ($detection) {
            return $this->formatDetection($detection);
        });

        return response()->json([
            'count' => $detections->count(),
            'data' => $detections,
        ]);
    }

    /**
     * 📱 Get detections by app_no OR farmer_id/farm_id (for Flutter sync)
     */
    public function getByAppNo(Request $request)
    {
        $appNo = $request->input('app_no');
        $farmerId = $request->input('farmer_id');
        $farmId = $request->input('farm_id');

        $query = PestAndDisease::with('validator:id,name');

        if (!empty($appNo)) {
            $query->where('app_no', $appNo);
        } elseif (!empty($farmerId)) {
            $query->where('farmer_id', $farmerId);
            if (!empty($farmId)) {
                $query->where('farm_id', $farmId);
            }
        } else {
            return response()->json([
                'error' => 'app_no or farmer_id is required',
            ], 400);
        }

        $detections = $query->orderBy('date_detected', 'desc')
            ->get()
            ->map(function ($detection) {
                return $this->formatDetection($detection);
            });

        return response()->json([
            'count' => $detections->count(),
            'data' => $detections,
        ]);
    }

    /**
     * 📄 Get single detection with validation details
     */
    public function show($id)
    {
        $detection = PestAndDisease::with('validator:id,name')->find($id);

        if (!$detection) {
            return response()->json([
                'message' => 'Detection not found',
            ], 404);
        }

        return response()->json([
            'data' => $this->formatDetection($detection),
        ]);
    }

    /**
     * 🔄 Check validation status for multiple detections (for Flutter sync)
     */
    public function checkValidationStatus(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json([
                'message' => 'No IDs provided',
                'data' => [],
            ]);
        }

        $detections = PestAndDisease::with('validator:id,name')
            ->whereIn('case_id', $ids)
            ->get()
            ->map(function ($detection) {
                return [
                    'case_id' => $detection->case_id,
                    'validation_status' => $detection->validation_status,
                    'expert_comments' => $detection->expert_comments,
                    'validated_by' => $detection->validator?->name,
                    'validated_at' => $detection->validated_at?->toISOString(),
                ];
            });

        return response()->json([
            'data' => $detections,
        ]);
    }

    /**
     * 🌾 Save farmer's action taken for a detection
     */
    public function saveFarmerAction(Request $request)
    {
        $validated = $request->validate([
            'detection_id'       => 'nullable|integer',
            'app_no'             => 'nullable|string',
            'farmer_action'      => 'required|string',
            'farmer_action_date' => 'nullable|string',
        ]);

        $query = PestAndDisease::query();

        if (!empty($validated['detection_id'])) {
            $query->where('case_id', $validated['detection_id']);
        } elseif (!empty($validated['app_no'])) {
            $query->where('app_no', $validated['app_no'])->latest();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'detection_id or app_no is required',
            ], 400);
        }

        $detection = $query->first();

        if (!$detection) {
            return response()->json([
                'success' => false,
                'message' => 'Detection not found',
            ], 404);
        }

        $detection->update([
            'farmer_action'      => $validated['farmer_action'],
            'farmer_action_date' => $validated['farmer_action_date'] ?? now(),
        ]);

        Log::info("Farmer action saved for detection #{$detection->case_id}", [
            'farmer_action' => $validated['farmer_action'],
        ]);

        // Notify Filament admin users
        try {
            $appNo    = $detection->app_no ?? $validated['app_no'] ?? 'Unknown';
            $pestName = $detection->pest ?? 'Detection';
            $preview  = Str::limit($validated['farmer_action'], 80);

            $viewUrl = route('filament.admin.resources.pest-and-diseases.index');

            $adminUsers = collect();
            try {
                $adminUsers = User::role(['super_admin', 'panel_user'])->get();
            } catch (\Exception $e) {
                $adminUsers = User::all();
            }

            foreach ($adminUsers as $user) {
                Notification::make()
                    ->title('Farmer Action Taken on Detection')
                    ->body("**{$appNo}** submitted action for **{$pestName}**: \"{$preview}\"")
                    ->icon('heroicon-o-check-circle')
                    ->iconColor('success')
                    ->actions([
                        Action::make('view')
                            ->label('View Detections')
                            ->url($viewUrl)
                            ->button()
                            ->markAsRead(),
                    ])
                    ->sendToDatabase($user);
            }
        } catch (\Exception $e) {
            Log::error('Error sending farmer action notification: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Farmer action saved successfully',
        ]);
    }

    /**
     * Format detection for API response
     */
    private function formatDetection($detection)
    {
        return [
            'case_id' => $detection->case_id,
            'app_no' => $detection->app_no,
            'expert_id' => $detection->expert_id,
            'pest' => $detection->pest,
            'type' => $detection->type,
            'confidence' => $detection->confidence,
            'severity' => $detection->severity,
            'latitude' => $detection->latitude,
            'longitude' => $detection->longitude,
            'area' => $detection->area,
            'date_detected' => $detection->date_detected,
            'image_url' => $detection->image_path
                ? Storage::disk('public')->url($detection->image_path)
                : null,
            'validation_status' => $detection->validation_status,
            'expert_comments' => $detection->expert_comments,
            'validated_by' => $detection->validator?->name,
            'validated_at' => $detection->validated_at?->toISOString(),
            'farmer_action' => $detection->farmer_action,
            'farmer_action_date' => $detection->farmer_action_date?->toISOString(),
            'created_at' => $detection->created_at?->toISOString(),
            'updated_at' => $detection->updated_at?->toISOString(),
        ];
    }
}
