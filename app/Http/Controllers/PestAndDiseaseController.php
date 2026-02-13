<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PestAndDisease;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
            'pest'          => 'required|string',
            'type'          => 'nullable|string|in:pest,disease',
            'confidence'    => 'required|numeric',
            'latitude'      => 'required|numeric',
            'longitude'     => 'required|numeric',
            'area'          => 'nullable|string',
            'date_detected' => 'nullable|string',
            'severity'      => 'nullable|string',
        ]);

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
            'created_at' => $detection->created_at?->toISOString(),
            'updated_at' => $detection->updated_at?->toISOString(),
        ];
    }
}
