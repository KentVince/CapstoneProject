<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SoilAnalysisResource\Pages;
use App\Models\AdminRecordView;
use App\Models\SoilAnalysis;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Services\GeminiService;

class SoilAnalysisResource extends Resource
{
    protected static ?string $model = SoilAnalysis::class;

    protected static ?string $navigationIcon  = 'heroicon-o-beaker';
    protected static ?string $navigationLabel = 'Soil Analysis';
    protected static ?string $pluralLabel     = 'Soil Analyses';
    protected static ?string $navigationGroup = 'Soil Fertility';
    protected static ?int    $navigationSort  = 2;
    /**
     * Hide from panel_user (default users)
     */
    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        // Hide from panel users
        return !$user->hasRole('panel_user');
    }

    public static function getNavigationBadge(): ?string
    {
        $userId = auth()->id();
        $count = static::getModel()::where('validation_status', 'pending')
            ->whereNotExists(function ($q) use ($userId) {
                $q->from('admin_record_views')
                    ->whereColumn('record_id', 'soil_analysis.id')
                    ->where('record_type', 'soil_analysis')
                    ->where('user_id', $userId);
            })
            ->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('SoilAnalysisTabs')
                ->tabs([
                    // ————————————————————— Farmer / Farm —————————————————————
                    Tab::make('Farmer & Farm')
                        ->schema([
                            Forms\Components\Hidden::make('farmer_id')
                                ->dehydrated(true),

                            // Preload farm data for instant client-side population
                            Placeholder::make('_farm_data_loader')
                                ->hiddenLabel()
                                ->columnSpanFull()
                                ->extraAttributes(['class' => 'hidden'])
                                ->content(function () {
                                    $json = \App\Models\Farm::with('farmer:id,last_name')
                                        ->get(['id', 'farm_name', 'soil_type', 'latitude', 'longtitude'])
                                        ->keyBy('id')
                                        ->map(fn ($f) => [
                                            'farm_name'        => $f->farm_name ?? '',
                                            'soil_type'        => $f->soil_type ?? '',
                                            'location'         => ($f->latitude && $f->longtitude) ? "{$f->latitude}, {$f->longtitude}" : '',
                                        ])
                                        ->toJson(JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                                    return new \Illuminate\Support\HtmlString(
                                        "<script>window.__cafarmFarms={$json};</script>"
                                    );
                                }),

                            Grid::make(3)->schema([
                                // Farm select - required field
                                Forms\Components\Select::make('farm_id')
                                    ->label('Farm')
                                    ->relationship('farm', 'farm_name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->extraInputAttributes([
                                        // Instantly populate Farm Name before the Livewire round-trip returns
                                        'x-on:change' => "
                                            const farm = window.__cafarmFarms?.[this.value];
                                            if (!farm) return;
                                            const nameEl = document.getElementById('cafarm-farm-name-input');
                                            if (nameEl) nameEl.value = farm.farm_name;
                                        ",
                                    ])
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        if ($state) {
                                            $farm = \App\Models\Farm::with('farmer:id,last_name')
                                                ->select(['id', 'farm_name', 'soil_type', 'latitude', 'longtitude', 'farmer_id'])
                                                ->find($state);
                                            if ($farm) {
                                                $set('farm_name', $farm->farm_name);

                                                if ($farm->farmer) {
                                                    $set('farmer_id', $farm->farmer->id);

                                                    // Generate sample_id: Lastname-Sample-MMDDYY-XX
                                                    $now = now();
                                                    $dateStr = $now->format('mdy');
                                                    $lastName = ucfirst(strtolower(trim($farm->farmer->last_name ?? 'Farmer')));
                                                    $prefix = "{$lastName}-Sample-{$dateStr}";

                                                    $existingCount = \App\Models\SoilAnalysis::where('sample_id', 'like', "{$prefix}-%")->count();
                                                    $sequence = str_pad($existingCount + 1, 2, '0', STR_PAD_LEFT);
                                                    $set('sample_id', "{$prefix}-{$sequence}");
                                                }
                                                if ($farm->soil_type) {
                                                    $set('soil_type', $farm->soil_type);
                                                }
                                                if ($farm->latitude && $farm->longtitude) {
                                                    $set('location', "{$farm->latitude}, {$farm->longtitude}");
                                                }
                                            }
                                        }
                                    }),

                                Forms\Components\TextInput::make('farm_name')
                                    ->label('Farm Name')
                                    ->maxLength(255)
                                    ->disabled()
                                    ->dehydrated(true)
                                    ->extraInputAttributes(['id' => 'cafarm-farm-name-input']),

                                     Forms\Components\Select::make('soil_type')
                                    ->options([
                                        'Clay' => 'Clay',
                                        'Loam' => 'Loam',
                                        'Sandy Loam' => 'Sandy Loam',
                                        'Silty Clay' => 'Silty Clay',
                                        'Other' => 'Other',
                                    ])
                                    ->native(false)
                                    ->required(),
                            ]),
                            Grid::make(3)->schema([
                               

                                Forms\Components\Select::make('analysis_type')
                                    ->label('Analysis Type')
                                    ->options([
                                        'with_lab' => 'With Lab',
                                        'without_lab' => 'Without Lab',
                                    ])
                                    ->native(false)
                                    ->live(),

                                Forms\Components\Select::make('crop_variety')
                                    ->label('Crop Variety')
                                    ->options([
                                        'Coffee - Arabica' => 'Coffee - Arabica',
                                        'Coffee - Robusta' => 'Coffee - Robusta',
                                        'Coffee - Excelsa/Liberica' => 'Coffee - Excelsa/Liberica',
                                    ])
                                    ->native(false)
                                    ->required(),

                                Forms\Components\TextInput::make('location')
                                    ->label('Sampling Location (GPS or Address)')
                                    ->maxLength(255)
                                    ->disabled()
                                    ->dehydrated(true),
                            ]),
                            Grid::make(3)->schema([
                                Forms\Components\DatePicker::make('date_collected')
                                    ->label('Date Collected')
                                    ->native(false),
                            ]),

                            Forms\Components\FileUpload::make('lab_file')
                                ->label('Laboratory Analysis Images')
                                ->helperText('Upload one or more images of the laboratory analysis result. Drag to reorder.')
                                ->disk('public')
                                ->directory('soil-lab-files')
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                                ->maxSize(10240)
                                ->multiple()
                                ->reorderable()
                                ->downloadable()
                                ->previewable()
                                ->imagePreviewHeight('150')
                                ->panelLayout('grid')
                                ->columnSpanFull()
                                ->visible(fn (Forms\Get $get) => $get('analysis_type') === 'with_lab'),
                        ]),

                    // ————————————————————— Lab Information —————————————————————
                    Tab::make('Lab Information')
                        ->schema([
                            Grid::make(3)->schema([
                                Forms\Components\TextInput::make('sample_id')
                                    ->label('Sample ID')
                                    ->maxLength(255)
                                    ->disabled()
                                    ->dehydrated(true),

                                Forms\Components\TextInput::make('ref_no')
                                    ->label('Reference No.')
                                    ->default(fn () => 'REF-'.strtoupper(Str::random(6)))
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('submitted_by')
                                    ->maxLength(255),

                                Forms\Components\DatePicker::make('date_submitted')
                                    ->native(false),
                            ]),
                            Grid::make(3)->schema([
                                Forms\Components\DatePicker::make('date_analyzed')
                                    ->native(false),

                                Forms\Components\TextInput::make('lab_no')
                                    ->label('Laboratory No.')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('field_no')
                                    ->label('Field No.')
                                    ->maxLength(255),
                            ]),
                        ]),

                    // ————————————————————— Soil Properties —————————————————————
                    Tab::make('Soil Properties')
                        ->schema([
                            Grid::make(4)->schema([
                                Forms\Components\TextInput::make('ph_level')
                                    ->label('Soil pH')
                                    ->numeric()
                                    ->inputMode('decimal')
                                    ->step('0.01')
                                    ->suffix('pH'),

                                Forms\Components\TextInput::make('organic_matter')
                                    ->label('Organic Matter')
                                    ->numeric()
                                    ->inputMode('decimal')
                                    ->step('0.01')
                                    ->suffix('%'),
                            ]),
                        ]),

                    // ————————————————————— Nutrients —————————————————————
                    Tab::make('Nutrients (NPK & P)')
                        ->schema([
                            Grid::make(4)->schema([
                                Forms\Components\TextInput::make('nitrogen')
                                    ->label('Nitrogen (N)')
                                    ->numeric()
                                    ->inputMode('decimal')
                                    ->step('0.01')
                                    ->suffix('%'),

                                Forms\Components\TextInput::make('phosphorus')
                                    ->label('Phosphorus (P)')
                                    ->numeric()
                                    ->inputMode('decimal')
                                    ->step('0.01')
                                    ->suffix('ppm'),

                                Forms\Components\TextInput::make('potassium')
                                    ->label('Potassium (K)')
                                    ->numeric()
                                    ->inputMode('decimal')
                                    ->step('0.01')
                                    ->suffix('ppm'),
                            ]),
                        ]),

                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordClasses(function (SoilAnalysis $record) {
                if ($record->validation_status !== 'pending') return '';
                $viewed = AdminRecordView::hasViewed(auth()->id(), 'soil_analysis', $record->getKey());
                return $viewed ? '' : 'new-unread-record';
            })
            ->columns([
                Tables\Columns\TextColumn::make('sample_id')
                    ->label('Sample ID')
                    ->toggleable()
                    ->searchable()
                    ->sortable(),

                    //   Tables\Columns\TextColumn::make('submitted_by')
                    // ->label('Submitted By')
                    // ->toggleable(),

                     Tables\Columns\TextColumn::make('date_collected')
                    ->date()
                    ->label('Date Collected'),

                // Tables\Columns\TextColumn::make('date_analyzed')
                //     ->date()
                //     ->label('Date Analyzed'),
 


                // Attributes
                // Tables\Columns\TextColumn::make('soil_type')
                //     ->badge()
                //     ->sortable(),

                Tables\Columns\TextColumn::make('analysis_type')
                    ->label('Analysis Type')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'with_lab' => 'With Lab',
                        'without_lab' => 'Without Lab',
                        default => $state,
                    })
                    ->color(fn (?string $state) => match ($state) {
                        'with_lab' => 'success',
                        'without_lab' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('validation_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'disapproved' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('validator.name')
                    ->label('Validated By')
                    ->placeholder('—'),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $user = auth()->user();
                if ($user && $user->isAgriculturalProfessional()) {
                    $professional = $user->agriculturalProfessional;
                    if ($professional && $professional->agency === 'MAGRO' && $professional->municipality) {
                        $query->whereHas('farmer', fn ($q) => $q->where('farmer_address_mun', $professional->municipality));
                    }
                }
            })
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('soil_type')
                    ->options([
                        'Clay' => 'Clay',
                        'Loam' => 'Loam',
                        'Sandy Loam' => 'Sandy Loam',
                        'Silty Clay' => 'Silty Clay',
                        'Other' => 'Other',
                    ]),
                Tables\Filters\TrashedFilter::make()
                    ->hidden(fn () => !in_array(SoftDeletingScope::class, class_uses_recursive(SoilAnalysis::class))),
            ])
                ->actions([
                    Tables\Actions\ActionGroup::make([
                        Action::make('view')
                            ->label('View Details')
                            ->icon('heroicon-o-eye')
                            ->color('info')
                            ->modalHeading(fn (SoilAnalysis $record) => "Soil Analysis: {$record->farm->name}")
                            ->modalWidth('6xl')
                            ->extraModalWindowAttributes(['class' => 'p-2'])
                            ->modalContent(function (SoilAnalysis $record) {
                                if ($record->validation_status === 'pending') {
                                    AdminRecordView::markViewed(auth()->id(), 'soil_analysis', $record->getKey());
                                }
                                // Mark related header notifications as read
                                auth()->user()?->unreadNotifications()
                                    ->where('data', 'like', '%viewRecord=' . $record->id . '%')
                                    ->update(['read_at' => now()]);
                                return view('filament.resources.soil-analysis.view-modal', ['record' => $record]);
                            })
                            ->modalFooterActions(fn (SoilAnalysis $record, Action $action) => [

                                // ── Expert Recommendation (manual entry) ──
                                Action::make('recommend')
                                    ->label('Expert Recommendation')
                                    ->icon('heroicon-o-star')
                                    ->color('info')
                                    ->size('lg')
                                    ->extraAttributes(['class' => 'px-8 py-3 mx-2'])
                                    ->form([
                                        Forms\Components\Select::make('validation_status')
                                            ->label('Status')
                                            ->options([
                                                'approved'    => 'Approve Analysis',
                                                'disapproved' => 'Request Revision',
                                            ])
                                            ->required(),
                                        Textarea::make('expert_comments')
                                            ->label('Expert Recommendation')
                                            ->placeholder('Enter your recommendations, suggestions, or reason for revision...')
                                            ->required()
                                            ->rows(20)
                                            ->autosize()
                                            ->extraAttributes(['style' => 'min-height: 400px; font-family: monospace; font-size: 0.85rem;']),
                                    ])
                                    ->action(function (array $data) use ($record, $action) {
                                        $record->update([
                                            'validation_status' => $data['validation_status'],
                                            'expert_comments'   => $data['expert_comments'],
                                            'validated_by'      => Auth::id(),
                                            'validated_at'      => now(),
                                        ]);

                                        $title = $data['validation_status'] === 'approved'
                                            ? 'Soil Analysis Approved with Recommendations'
                                            : 'Revision Requested with Expert Recommendations';

                                        Notification::make()
                                            ->title($title)
                                            ->success()
                                            ->send();
                                        $action->cancel();
                                    })
                                    ->visible(fn () => $record->validation_status === 'pending'),

                                Action::make('addExpertComment')
                                    ->label('Add Expert Comment')
                                    ->icon('heroicon-o-chat-bubble-left-right')
                                    ->color('warning')
                                    ->size('lg')
                                    ->extraAttributes(['class' => 'px-8 py-3 mx-2'])
                                    ->form([
                                        Textarea::make('message')
                                            ->label('Expert Comment / Recommendation')
                                            ->placeholder('Enter your additional recommendation or comment...')
                                            ->required()
                                            ->rows(4),
                                    ])
                                    ->action(function (array $data) use ($record) {
                                        \App\Models\SoilAnalysisExpertComment::create([
                                            'soil_analysis_id' => $record->id,
                                            'user_id'          => Auth::id(),
                                            'message'          => $data['message'],
                                        ]);

                                        Notification::make()
                                            ->title('Expert Comment Added')
                                            ->body('Your comment has been added to the conversation thread.')
                                            ->success()
                                            ->send();
                                    })
                                    ->visible(fn () => $record->validation_status !== 'pending'),

                                // ── Generate AI Draft (Gemini) — saves directly to PDF viewer ──
                                Action::make('generateAiDraft')
                                    ->label('Generate AI Draft')
                                    ->icon('heroicon-o-sparkles')
                                    ->color('warning')
                                    ->size('lg')
                                    ->extraAttributes(['class' => 'px-8 py-3 mx-2'])
                                    ->tooltip('Uses Google Gemini AI to generate a recommendation. Result will appear in the AI Recommendation PDF viewer below.')
                                    ->disabled(fn () => $record->analysis_type === 'without_lab')
                                    ->action(function () use ($record) {
                                        if (!config('services.gemini.api_key')) {
                                            Notification::make()
                                                ->title('API Key Not Configured')
                                                ->body('Please set GEMINI_API_KEY in your .env file.')
                                                ->danger()
                                                ->send();
                                            return;
                                        }

                                        $soilData = [
                                            'soil_type'      => $record->soil_type,
                                            'crop_variety'   => $record->crop_variety,
                                            'ph_level'       => $record->ph_level,
                                            'organic_matter' => $record->organic_matter,
                                            'nitrogen'       => $record->nitrogen,
                                            'phosphorus'     => $record->phosphorus,
                                            'potassium'      => $record->potassium,
                                            'farm_name'      => $record->farm_name,
                                            'location'       => $record->location,
                                            'analysis_type'  => $record->analysis_type,
                                        ];

                                        $fields = app(GeminiService::class)->generateSoilFields($soilData);

                                        $record->update([
                                            'ai_diagnosis'            => $fields['diagnosis'],
                                            'ai_farmer_summary'       => $fields['farmer_summary'],
                                            'ai_key_concerns'         => $fields['key_concerns'],
                                            'ai_priority_actions'     => $fields['priority_actions'],
                                            'ai_soil_remarks'         => $fields['soil_remarks'],
                                            'ai_organic_alternatives' => $fields['organic_alternatives'],
                                            'ai_practices'            => $fields['practices'],
                                            'ai_monitoring_plan'      => $fields['monitoring_plan'],
                                            'ai_expected_outcomes'    => $fields['expected_outcomes'],
                                            'ai_reminders'            => $fields['reminders'],
                                        ]);

                                        Notification::make()
                                            ->title('AI Recommendation Generated')
                                            ->body('The AI recommendation is now displayed in the modal.')
                                            ->success()
                                            ->send();
                                    })
                                    ->visible(fn () => $record->analysis_type !== 'without_lab'),

                            ])
                            ->modalFooterActionsAlignment(\Filament\Support\Enums\Alignment::Center),
                        Tables\Actions\EditAction::make()
                            ->modalWidth('7xl'),
                        Tables\Actions\DeleteAction::make(),
                    ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->tooltip('Actions')
                    ->button()
                    ->color('gray')
                    ->label('') // no button text
                ])
                ->actionsColumnLabel('Action') // ✅ this adds the header label
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->poll('10s') // Auto-refresh every 10 seconds when Flutter syncs new data
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSoilAnalyses::route('/'),
        ];
    }
}
