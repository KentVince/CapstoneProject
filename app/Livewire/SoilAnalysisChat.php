<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\SoilAnalysis;
use App\Models\SoilAnalysisConversation;
use App\Models\MobileUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SoilAnalysisChat extends Component
{
    public int $soilAnalysisId;
    public string $newMessage = '';

    public function mount(int $soilAnalysisId): void
    {
        $this->soilAnalysisId = $soilAnalysisId;

        // Mark farmer messages as read when expert opens
        SoilAnalysisConversation::where('soil_analysis_id', $soilAnalysisId)
            ->where('sender_type', 'farmer')
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function sendMessage(): void
    {
        $this->validate([
            'newMessage' => 'required|string|max:2000',
        ]);

        $conversation = SoilAnalysisConversation::create([
            'soil_analysis_id' => $this->soilAnalysisId,
            'sender_type' => 'expert',
            'sender_id' => Auth::id(),
            'message' => $this->newMessage,
        ]);

        // Send FCM to farmer
        $this->sendFcmToFarmer($conversation);

        $this->newMessage = '';
    }

    private function sendFcmToFarmer(SoilAnalysisConversation $conversation): void
    {
        try {
            $analysis = SoilAnalysis::with('farmer')->find($this->soilAnalysisId);
            $farmer = $analysis?->farmer;
            if (!$farmer) return;

            $mobileUser = MobileUser::where('app_no', $farmer->app_no)->first();
            if (!$mobileUser || !$mobileUser->fcm_token) return;

            $messaging = app('firebase.messaging');
            $expertName = Auth::user()->name ?? 'Expert';

            $message = \Kreait\Firebase\Messaging\CloudMessage::fromArray([
                'token' => $mobileUser->fcm_token,
                'notification' => [
                    'title' => 'New Message from Expert',
                    'body' => "{$expertName}: " . Str::limit($conversation->message, 100),
                ],
                'data' => [
                    'type' => 'soil_conversation_message',
                    'analysis_id' => (string) $this->soilAnalysisId,
                    'message_id' => (string) $conversation->id,
                ],
            ]);

            $messaging->send($message);
        } catch (\Exception $e) {
            Log::error('FCM conversation notification error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $analysis = SoilAnalysis::with(['conversations', 'farmer', 'validator'])->find($this->soilAnalysisId);

        return view('livewire.soil-analysis-chat', [
            'analysis' => $analysis,
        ]);
    }
}
