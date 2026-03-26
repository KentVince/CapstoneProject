<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\PestAndDisease;

class PestDiseaseApprovalModal extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];
    public bool $isOpen = false;
    public ?PestAndDisease $record = null;
    public ?int $recordId = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('record_id')
                    ->default($this->recordId),
                
                Section::make('Validation')
                    ->schema([
                        Textarea::make('expert_comments')
                            ->label('Comments (Required for Disapproval)')
                            ->placeholder('Please provide reason for disapproval...')
                            ->rows(3),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    #[On('openApprovalModal')]
    public function openModal(?int $recordId = null): void
    {
        if ($recordId) {
            $this->recordId = $recordId;
        }

        if ($this->recordId) {
            $this->record = PestAndDisease::find($this->recordId);
        }

        $this->isOpen = true;
        $this->form->fill();
        $this->dispatch('modal-opened')->self();
    }

    public function closeModal(): void
    {
        $this->isOpen = false;
        $this->record = null;
        $this->recordId = null;
        $this->form->fill();
    }

    public function approve(): void
    {
        if (!$this->record) {
            return;
        }

        $this->record->update([
            'validation_status' => 'approved',
            'expert_comments' => null,
            'validated_by' => Auth::id(),
            'validated_at' => now(),
        ]);

        Notification::make()
            ->title('Detection Approved')
            ->body('The detection has been approved successfully.')
            ->success()
            ->send();

        $this->closeModal();
        $this->dispatch('refresh-table');
    }

    public function disapprove(): void
    {
        if (!$this->record) {
            return;
        }

        $data = $this->form->getState();

        if (empty($data['expert_comments'])) {
            Notification::make()
                ->title('Comments Required')
                ->body('Please provide comments for disapproval.')
                ->warning()
                ->send();
            return;
        }

        $this->record->update([
            'validation_status' => 'disapproved',
            'expert_comments' => $data['expert_comments'],
            'validated_by' => Auth::id(),
            'validated_at' => now(),
        ]);

        Notification::make()
            ->title('Detection Disapproved')
            ->body('The detection has been disapproved and comments have been saved.')
            ->warning()
            ->send();

        $this->closeModal();
        $this->dispatch('refresh-table');
    }

    public function render()
    {
        return view('livewire.pest-disease-approval-modal');
    }
}
