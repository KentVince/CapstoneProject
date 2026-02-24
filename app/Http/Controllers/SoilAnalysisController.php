<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SoilAnalysis;
use App\Models\SoilAnalysisConversation;
use App\Models\Farmer;
use App\Models\User;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class SoilAnalysisController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'farmer_id'      => 'required|integer',
            'farm_id'        => 'nullable|integer',
            'sample_id'      => 'nullable|string',
            'farm_name'      => 'nullable|string',
            'crop_variety'   => 'nullable|string',
            'soil_type'      => 'nullable|string',
            'analysis_type'  => 'nullable|string|in:with_lab,without_lab',
            'date_collected' => 'nullable|date',
            'location'       => 'nullable|string',
            'ref_no'         => 'nullable|string',
            'submitted_by'   => 'nullable|string',
            'date_submitted' => 'nullable|string',
            'date_analyzed'  => 'nullable|string',
            'lab_no'         => 'nullable|string',
            'field_no'       => 'nullable|string',
            'ph_level'       => 'nullable|numeric',
            'nitrogen'       => 'nullable|numeric',
            'phosphorus'     => 'nullable|numeric',
            'potassium'      => 'nullable|numeric',
            'organic_matter' => 'nullable|numeric',
            'recommendation' => 'nullable|string',
                'validation_status' => 'nullable|string',
                'expert_comments' => 'nullable|string',
                'validated_by' => 'nullable|string',
                'validated_at' => 'nullable|date',

        ]);

        $analysis = SoilAnalysis::create($data);

        // Notification is handled by SoilAnalysisObserver::created()

        return response()->json([
            'message' => 'Soil analysis saved successfully',
            'id' => $analysis->id,
            'soil_analysis_id' => $analysis->id,
        ]);
    }

    /**
     * Retrieve all soil analysis records with expert recommendations
     */
    public function index(Request $request)
    {
        $appNo = $request->query('app_no');
        
        $query = SoilAnalysis::query();

        // Filter by app_no if provided (for farmer's own records)
        if ($appNo) {
            $farmer = Farmer::where('app_no', $appNo)->first();
            if ($farmer) {
                $query->where('farmer_id', $farmer->id);
            }
        }

        $analyses = $query->get()
            ->map(fn ($analysis) => $this->formatAnalysis($analysis));

        return response()->json([
            'data' => $analyses,
            'count' => $analyses->count(),
        ]);
    }

    /**
     * Get soil analyses by app_no for mobile app
     * Returns all soil analyses with expert recommendations
     */
    public function getByAppNo(Request $request)
    {
        try {
            $appNo = $request->input('app_no');

            if (empty($appNo)) {
                return response()->json([
                    'success' => false,
                    'message' => 'app_no is required',
                ], 400);
            }

            \Log::info("Fetching soil analyses for app_no: {$appNo}");

            // Find farmer by app_no
            $farmer = Farmer::where('app_no', $appNo)->first();

            if (!$farmer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Farmer not found',
                    'data' => [],
                ], 404);
            }

            // Query soil analyses for this farmer
            $analyses = SoilAnalysis::where('farmer_id', $farmer->id)
                ->with(['validator', 'farmer'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($analysis) {
                    return [
                        'id' => $analysis->id,
                        'sample_id' => $analysis->sample_id,
                        'app_no' => $analysis->farmer?->app_no ?? null,
                        'location' => $analysis->location,
                        'date' => $analysis->date_collected?->toISOString(),
                        'analysis_type' => $analysis->analysis_type,
                        // Map our field names to what Flutter expects
                        'expert_recommendation' => $analysis->expert_comments,
                        'recommended_by' => $analysis->validator?->name,
                        'recommendation_date' => $analysis->validated_at?->toISOString(),
                        // Additional fields for the Flutter app
                        'farm_name' => $analysis->farm_name,
                        'crop_variety' => $analysis->crop_variety,
                        'soil_type' => $analysis->soil_type,
                        'ph_level' => $analysis->ph_level,
                        'nitrogen' => $analysis->nitrogen,
                        'phosphorus' => $analysis->phosphorus,
                        'potassium' => $analysis->potassium,
                        'organic_matter' => $analysis->organic_matter,
                        'recommendation' => $analysis->recommendation,
                        'validation_status' => $analysis->validation_status,
                        'farmer_reply' => $analysis->farmer_reply,
                        'farmer_reply_date' => $analysis->farmer_reply_date?->toISOString(),
                        'conversation_count' => $analysis->conversations()->count(),
                        'unread_expert_messages' => $analysis->conversations()
                            ->where('sender_type', 'expert')
                            ->where('is_read', false)
                            ->count(),
                        'created_at' => $analysis->created_at?->toISOString(),
                        'updated_at' => $analysis->updated_at?->toISOString(),
                    ];
                });

            \Log::info("Found {$analyses->count()} soil analyses for app_no: {$appNo}");

            return response()->json([
                'success' => true,
                'data' => $analyses,
            ], 200);

        } catch (\Exception $e) {
            \Log::error("Error fetching soil analyses: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch soil analyses',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Save farmer reply to a soil analysis expert recommendation
     */
    public function farmerReply(Request $request)
    {
        $request->validate([
            'analysis_id' => 'required',
            'farmer_reply' => 'required|string',
        ]);

        $analysis = SoilAnalysis::find($request->analysis_id);

        if (!$analysis) {
            return response()->json(['message' => 'Analysis not found'], 404);
        }

        $analysis->update([
            'farmer_reply' => $request->farmer_reply,
            'farmer_reply_date' => now(),
        ]);

        \Log::info("Farmer replied to soil analysis #{$analysis->id}: {$request->farmer_reply}");

        // Notify admins/experts about the farmer's action taken
        try {
            $farmer = $analysis->farmer;
            $farmerName = $farmer
                ? trim("{$farmer->firstname} {$farmer->lastname}")
                : "Farmer #{$analysis->farmer_id}";
            $farmName = $analysis->farm_name ?? 'Unknown Farm';

            $viewUrl = route('filament.admin.resources.soil-analyses.index', [
                'viewRecord' => $analysis->id,
            ]);

            $adminUsers = collect();
            try {
                $adminUsers = User::role(['super_admin', 'panel_user'])->get();
            } catch (\Exception $e) {
                $adminUsers = User::all();
            }

            foreach ($adminUsers as $user) {
                Notification::make()
                    ->title('Farmer Action Taken')
                    ->body("**{$farmerName}** replied on soil analysis for **{$farmName}**: \"{$request->farmer_reply}\"")
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->iconColor('info')
                    ->actions([
                        Action::make('view')
                            ->label('View Details')
                            ->url($viewUrl)
                            ->button()
                            ->markAsRead(),
                    ])
                    ->sendToDatabase($user);
            }
        } catch (\Exception $e) {
            \Log::error('Error sending farmer reply notification: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Reply saved successfully',
            'id' => $analysis->id,
        ], 200);
    }

    /**
     * Get the full conversation thread for a soil analysis.
     */
    public function getConversations(Request $request, $id)
    {
        $analysis = SoilAnalysis::with(['conversations', 'farmer', 'validator'])->find($id);

        if (!$analysis) {
            return response()->json(['success' => false, 'message' => 'Analysis not found'], 404);
        }

        // Mark expert messages as read when farmer fetches
        $appNo = $request->input('app_no');
        if ($appNo) {
            $analysis->conversations()
                ->where('sender_type', 'expert')
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        // Prepend initial expert_comments and farmer_reply as "virtual" messages
        $initialMessages = collect();

        if ($analysis->expert_comments) {
            $initialMessages->push([
                'id' => 0,
                'sender_type' => 'expert',
                'sender_name' => $analysis->validator?->name ?? 'Expert',
                'message' => $analysis->expert_comments,
                'is_read' => true,
                'created_at' => $analysis->validated_at?->toISOString() ?? $analysis->updated_at->toISOString(),
                'is_initial' => true,
            ]);
        }

        if ($analysis->farmer_reply) {
            $initialMessages->push([
                'id' => 0,
                'sender_type' => 'farmer',
                'sender_name' => $analysis->farmer
                    ? trim("{$analysis->farmer->firstname} {$analysis->farmer->lastname}")
                    : 'Farmer',
                'message' => $analysis->farmer_reply,
                'is_read' => true,
                'created_at' => $analysis->farmer_reply_date?->toISOString() ?? $analysis->updated_at->toISOString(),
                'is_initial' => true,
            ]);
        }

        $messages = $analysis->conversations->map(fn ($msg) => [
            'id' => $msg->id,
            'sender_type' => $msg->sender_type,
            'sender_name' => $msg->sender_name,
            'message' => $msg->message,
            'is_read' => $msg->is_read,
            'created_at' => $msg->created_at->toISOString(),
            'is_initial' => false,
        ]);

        return response()->json([
            'success' => true,
            'data' => $initialMessages->concat($messages)->values(),
        ]);
    }

    /**
     * Farmer sends a new message in the conversation.
     */
    public function storeConversation(Request $request, $id)
    {
        $request->validate([
            'app_no' => 'required|string',
            'message' => 'required|string|max:2000',
        ]);

        $analysis = SoilAnalysis::find($id);
        if (!$analysis) {
            return response()->json(['success' => false, 'message' => 'Analysis not found'], 404);
        }

        $farmer = Farmer::where('app_no', $request->app_no)->first();
        if (!$farmer || $farmer->id !== $analysis->farmer_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $conversation = SoilAnalysisConversation::create([
            'soil_analysis_id' => $analysis->id,
            'sender_type' => 'farmer',
            'sender_id' => $farmer->id,
            'message' => $request->message,
        ]);

        // Notify admins via Filament notification
        $this->notifyAdminsOfFarmerMessage($analysis, $farmer, $request->message);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $conversation->id,
                'sender_type' => $conversation->sender_type,
                'sender_name' => trim("{$farmer->firstname} {$farmer->lastname}"),
                'message' => $conversation->message,
                'created_at' => $conversation->created_at->toISOString(),
            ],
        ]);
    }

    /**
     * Notify all admin/expert users about a new farmer message.
     */
    private function notifyAdminsOfFarmerMessage(SoilAnalysis $analysis, Farmer $farmer, string $message): void
    {
        try {
            $farmerName = trim("{$farmer->firstname} {$farmer->lastname}");
            $farmName = $analysis->farm_name ?? 'Unknown Farm';
            $preview = Str::limit($message, 80);

            $viewUrl = route('filament.admin.resources.soil-analyses.index', [
                'viewRecord' => $analysis->id,
            ]);

            $adminUsers = collect();
            try {
                $adminUsers = User::role(['super_admin', 'panel_user'])->get();
            } catch (\Exception $e) {
                $adminUsers = User::all();
            }

            foreach ($adminUsers as $user) {
                Notification::make()
                    ->title('New Message from Farmer')
                    ->body("**{$farmerName}** on soil analysis for **{$farmName}**: \"{$preview}\"")
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->iconColor('info')
                    ->actions([
                        Action::make('view')
                            ->label('View Conversation')
                            ->url($viewUrl)
                            ->button()
                            ->markAsRead(),
                    ])
                    ->sendToDatabase($user);
            }
        } catch (\Exception $e) {
            \Log::error('Error sending conversation notification: ' . $e->getMessage());
        }
    }

    /**
     * Format soil analysis for API response including expert recommendations
     */
    private function formatAnalysis($analysis)
    {
        return [
            'id' => $analysis->id,
            'farmer_id' => $analysis->farmer_id,
            'farm_id' => $analysis->farm_id,
            'farm_name' => $analysis->farm_name,
            'crop_variety' => $analysis->crop_variety,
            'soil_type' => $analysis->soil_type,
            'analysis_type' => $analysis->analysis_type,
            'date_collected' => $analysis->date_collected?->toISOString(),
            'location' => $analysis->location,
            'ref_no' => $analysis->ref_no,
            'submitted_by' => $analysis->submitted_by,
            'date_submitted' => $analysis->date_submitted?->toISOString(),
            'date_analyzed' => $analysis->date_analyzed?->toISOString(),
            'lab_no' => $analysis->lab_no,
            'field_no' => $analysis->field_no,
            'ph_level' => $analysis->ph_level,
            'nitrogen' => $analysis->nitrogen,
            'phosphorus' => $analysis->phosphorus,
            'potassium' => $analysis->potassium,
            'organic_matter' => $analysis->organic_matter,
            'recommendation' => $analysis->recommendation,
            'validation_status' => $analysis->validation_status,
            'expert_comments' => $analysis->expert_comments,
            'validated_by' => $analysis->validator?->name,
            'validated_at' => $analysis->validated_at?->toISOString(),
            'created_at' => $analysis->created_at?->toISOString(),
            'updated_at' => $analysis->updated_at?->toISOString(),
        ];
    }
}
