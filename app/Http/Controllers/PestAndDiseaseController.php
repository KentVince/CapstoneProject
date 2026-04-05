<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Farm;
use App\Models\Farmer;
use App\Models\PestAndDisease;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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
            'severity'           => 'nullable|string',
            'pest_incidence'     => 'nullable|numeric',
            'incidence_rating'   => 'nullable|string',
            'pest_severity_pct'  => 'nullable|numeric',
            'sum_ratings'        => 'nullable|integer',
            'n_infested'         => 'nullable|integer',
            'n_total'            => 'nullable|integer',
            'total_trees_planted' => 'nullable|integer',
            'scan_results'       => 'nullable|string',
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
            'id'      => $detection->case_id,
            'data'    => $detection,
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
        $query = PestAndDisease::with('validator.agriculturalProfessional')->latest();

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

        $query = PestAndDisease::with('validator.agriculturalProfessional');

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
        $detection = PestAndDisease::with('validator.agriculturalProfessional')->find($id);

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

        $detections = PestAndDisease::with('validator.agriculturalProfessional')
            ->whereIn('case_id', $ids)
            ->get()
            ->map(function ($detection) {
                return [
                    'case_id' => $detection->case_id,
                    'validation_status' => $detection->validation_status,
                    'expert_comments' => $detection->expert_comments,
                    'validated_by' => $detection->validator?->agriculturalProfessional
                        ? trim($detection->validator->agriculturalProfessional->firstname . ' ' . $detection->validator->agriculturalProfessional->lastname)
                        : $detection->validator?->name,
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

            $viewUrl = route('filament.admin.resources.pest-and-diseases.index', [], false)
                . '?viewRecord=' . $detection->case_id . '&scrollTo=conversation';

            $adminUsers = collect();
            try {
                $adminUsers = User::role(['super_admin', 'panel_user', 'agri_expert'])->get();
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
                            ->label('View Action Taken')
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
     * 💬 Expert adds an additional comment to a detection (from Flutter)
     */
    public function addExpertComment(Request $request, $id)
    {
        $request->validate([
            'professional_id' => 'required|integer|exists:agricultural_professionals,id',
            'message'         => 'required|string|max:2000',
        ]);

        $detection = PestAndDisease::find($id);
        if (!$detection) {
            return response()->json(['success' => false, 'message' => 'Detection not found'], 404);
        }

        if ($detection->validation_status === 'pending') {
            return response()->json(['success' => false, 'message' => 'Case is still pending approval'], 422);
        }

        // Resolve the panel User linked to this AgriculturalProfessional
        $user = User::whereHas('agriculturalProfessional', fn ($q) =>
            $q->where('id', $request->professional_id)
        )->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Expert user account not found'], 404);
        }

        $comment = \App\Models\PestDiseaseExpertComment::create([
            'pest_and_disease_id' => $detection->case_id,
            'user_id'             => $user->id,
            'message'             => $request->message,
        ]);

        $agency = $user->agriculturalProfessional?->agency;
        $agencyLabel = $agency ? "Expert from {$agency}" : $user->name;
        // FCM is sent automatically by PestDiseaseExpertCommentObserver::created()

        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully',
            'data' => [
                'id'           => $comment->id,
                'sender_type'  => 'expert',
                'sender_name'  => $user->name,
                'agency'       => $agency,
                'agency_label' => $agencyLabel,
                'message'      => $comment->message,
                'created_at'   => $comment->created_at->toISOString(),
                'is_initial'   => false,
                'is_extra_comment' => true,
            ],
        ]);
    }

    /**
     * Get nearby severe/high pest detections that have been approved.
     * Used by Flutter to poll for outbreak alerts near the farmer's farm.
     */
    public function getNearbySevere(Request $request)
    {
        $request->validate([
            'latitude'       => 'required|numeric',
            'longitude'      => 'required|numeric',
            'radius_km'      => 'nullable|numeric|min:1|max:50',
            'exclude_app_no' => 'nullable|string',
        ]);

        $lat = $request->input('latitude');
        $lng = $request->input('longitude');
        $radiusKm = $request->input('radius_km', 10);
        $excludeAppNo = $request->input('exclude_app_no');

        // Haversine formula to calculate distance in km
        $haversine = "(6371 * acos(
            cos(radians(?)) * cos(radians(latitude)) *
            cos(radians(longitude) - radians(?)) +
            sin(radians(?)) * sin(radians(latitude))
        ))";

        $query = PestAndDisease::select('*')
            ->selectRaw("{$haversine} AS distance_km", [$lat, $lng, $lat])
            ->where('validation_status', 'approved')
            ->whereIn('severity', ['High', 'Severe'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', 0)
            ->where('longitude', '!=', 0)
            ->having('distance_km', '<=', $radiusKm)
            ->orderBy('date_detected', 'desc')
            ->limit(50);

        if ($excludeAppNo) {
            $query->where('app_no', '!=', $excludeAppNo);
        }

        $detections = $query->get()->map(function ($d) {
            return [
                'id'                => $d->case_id,
                'pest'              => $d->pest,
                'type'              => $d->type,
                'severity'          => $d->severity,
                'confidence'        => $d->confidence,
                'latitude'          => $d->latitude,
                'longitude'         => $d->longitude,
                'area'              => $d->area,
                'date_detected'     => $d->date_detected,
                'validation_status' => $d->validation_status,
                'distance_km'       => round($d->distance_km, 2),
                'farm_name'         => $d->farm?->farm_name ?? 'Unknown Farm',
            ];
        });

        return response()->json([
            'count' => $detections->count(),
            'data'  => $detections,
        ]);
    }

    /**
     * Format detection for API response
     */
    private function formatDetection($detection)
    {
        // Load extra expert comments
        $extraComments = \App\Models\PestDiseaseExpertComment::with('expert.agriculturalProfessional')
            ->where('pest_and_disease_id', $detection->case_id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($c) {
                $agency = $c->expert?->agriculturalProfessional?->agency;
                return [
                    'id'              => $c->id,
                    'sender_type'     => 'expert',
                    'sender_name'     => $c->expert?->name ?? 'Expert',
                    'agency'          => $agency,
                    'agency_label'    => $agency ? "Expert from {$agency}" : null,
                    'message'         => $c->message,
                    'created_at'      => $c->created_at->toISOString(),
                    'is_initial'      => false,
                    'is_extra_comment' => true,
                ];
            });

        // Build conversation thread (initial approval + extra comments + farmer action)
        $thread = collect();

        if ($detection->expert_comments) {
            $agency = $detection->validator?->agriculturalProfessional?->agency;
            $thread->push([
                'id'           => 0,
                'sender_type'  => 'expert',
                'sender_name'  => $detection->validator?->agriculturalProfessional
                    ? trim($detection->validator->agriculturalProfessional->firstname . ' ' . $detection->validator->agriculturalProfessional->lastname)
                    : ($detection->validator?->name ?? 'Expert'),
                'agency'       => $agency,
                'agency_label' => $agency ? "Expert from {$agency}" : null,
                'message'      => $detection->expert_comments,
                'created_at'   => $detection->validated_at?->toISOString(),
                'is_initial'   => true,
            ]);
        }

        foreach ($extraComments as $c) {
            $thread->push($c);
        }

        if ($detection->farmer_action) {
            $thread->push([
                'id'           => 0,
                'sender_type'  => 'farmer',
                'sender_name'  => null,
                'agency'       => null,
                'agency_label' => null,
                'message'      => $detection->farmer_action,
                'created_at'   => $detection->farmer_action_date?->toISOString(),
                'is_initial'   => true,
            ]);
        }

        return [
            'id'     => $detection->case_id,
            'case_id' => $detection->case_id,
            'app_no' => $detection->app_no,
            'farm_id' => $detection->farm_id,
            'farmer_id' => $detection->farmer_id,
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
            'image_path_relative' => $detection->image_path
                ? 'storage/' . $detection->image_path
                : null,
            'validation_status' => $detection->validation_status,
            'expert_comments' => $detection->expert_comments,
            'validated_by' => $detection->validator?->agriculturalProfessional
                ? trim($detection->validator->agriculturalProfessional->firstname . ' ' . $detection->validator->agriculturalProfessional->lastname)
                : $detection->validator?->name,
            'validated_by_agency' => $detection->validator?->agriculturalProfessional?->agency,
            'validated_at' => $detection->validated_at?->toISOString(),
            'farmer_action' => $detection->farmer_action,
            'farmer_action_date' => $detection->farmer_action_date?->toISOString(),
            'pest_incidence' => $detection->pest_incidence,
            'incidence_rating' => $detection->incidence_rating,
            'pest_severity_pct' => $detection->pest_severity_pct,
            'sum_ratings' => $detection->sum_ratings,
            'n_infested' => $detection->n_infested,
            'n_total' => $detection->n_total,
            'total_trees_planted' => $detection->total_trees_planted,
            'conversation_thread' => $thread->sortBy('created_at')->values(),
            'extra_expert_comments_count' => $extraComments->count(),
            'created_at' => $detection->created_at?->toISOString(),
            'updated_at' => $detection->updated_at?->toISOString(),
        ];
    }
}
