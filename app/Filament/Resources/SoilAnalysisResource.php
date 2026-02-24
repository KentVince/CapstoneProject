<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SoilAnalysisResource\Pages;
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
use App\Services\SoilRecommendationService;
use App\Services\NoLabRecommendationService;

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
                            Grid::make(3)->schema([
                                // Farm select - required field
                                Forms\Components\Select::make('farm_id')        
                                    ->label('Farm')
                                    ->relationship('farm', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        if ($state) {
                                            $farm = \App\Models\Farm::with('farmer')->find($state);
                                            if ($farm) {
                                                // Set farm_name from farm's name
                                                $set('farm_name', $farm->name);
                                                
                                                // Set farmer_id from farm's farmer relationship
                                                if ($farm->farmer) {
                                                    $set('farmer_id', $farm->farmer->id);
                                                    
                                                    // Generate unique sample_id: W1-{farmer_id}-{MM}-{DD}-{sequence}
                                                    $now = now();
                                                    $month = $now->format('m');
                                                    $day = $now->format('d');
                                                    $farmerId = str_pad($farm->farmer->id, 2, '0', STR_PAD_LEFT);
                                                    
                                                    // Get the count of samples created today for this farmer to make it unique
                                                    $todayCount = \App\Models\SoilAnalysis::where('farmer_id', $farm->farmer->id)
                                                        ->whereDate('created_at', $now->toDateString())
                                                        ->count() + 1;
                                                    $sequence = str_pad($todayCount, 2, '0', STR_PAD_LEFT);
                                                    
                                                    $sampleId = "W1-{$farmerId}-{$month}-{$day}-{$sequence}";
                                                    $set('sample_id', $sampleId);
                                                }
                                                // Set soil type from farm
                                                if ($farm->soil_type) {
                                                    $set('soil_type', $farm->soil_type);
                                                }
                                                // Set location/GPS from farm coordinates (latitude and longitude)
                                                if ($farm->latitude && $farm->longitude) {
                                                    $set('location', "{$farm->latitude}, {$farm->longitude}");
                                                }
                                            }
                                        }
                                    }),

                                Forms\Components\TextInput::make('farm_name')   
                                    ->label('Farm Name')    
                                    ->maxLength(255)
                                    ->disabled()
                                    ->dehydrated(true),

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
                                    ->native(false),

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

                    // ————————————————————— Recommendation —————————————————————
                    Tab::make('Recommendation')
                        ->schema([
                            Forms\Components\Actions::make([
                                // ── Static recommendation (PDF-based, instant) ──
                                Forms\Components\Actions\Action::make('generateStaticRecommendation')
                                    ->label('Generate Recommendation (Guide-Based)')
                                    ->icon('heroicon-o-document-text')
                                    ->color('warning')
                                    ->tooltip('For WITH LAB: uses BSWM/FAO soil interpretation guidelines. For WITHOUT LAB: uses the KAPE non-bearing coffee fertilizer guide based on soil type.')
                                    ->action(function (Forms\Get $get, Forms\Set $set) {
                                        $soilData = [
                                            'soil_type'      => $get('soil_type'),
                                            'crop_variety'   => $get('crop_variety'),
                                            'ph_level'       => $get('ph_level'),
                                            'organic_matter' => $get('organic_matter'),
                                            'nitrogen'       => $get('nitrogen'),
                                            'phosphorus'     => $get('phosphorus'),
                                            'potassium'      => $get('potassium'),
                                        ];

                                        $isNoLab = $get('analysis_type') === 'without_lab';

                                        if ($isNoLab) {
                                            $recommendation = app(NoLabRecommendationService::class)->generate($soilData);
                                            $body = 'Based on KAPE / BSWM non-bearing coffee guide (no laboratory). Review before saving.';
                                        } else {
                                            $recommendation = app(SoilRecommendationService::class)->generate($soilData);
                                            $body = 'Based on BSWM/FAO Philippine soil guidelines. Review and edit before saving.';
                                        }

                                        $set('recommendation', $recommendation);

                                        Notification::make()
                                            ->title('Recommendation Generated')
                                            ->body($body)
                                            ->success()
                                            ->send();
                                    }),

                                // ── AI recommendation (Gemini, requires API key) ──
                                Forms\Components\Actions\Action::make('generateAiRecommendation')
                                    ->label('Generate AI Recommendation')
                                    ->icon('heroicon-o-sparkles')
                                    ->color('success')
                                    ->tooltip('Uses Google Gemini AI to generate a recommendation based on the soil analysis values entered above.')
                                    ->disabled(fn (Forms\Get $get) => $get('analysis_type') === 'without_lab')
                                    ->action(function (Forms\Get $get, Forms\Set $set) {
                                        if (!config('services.gemini.api_key')) {
                                            Notification::make()
                                                ->title('API Key Not Configured')
                                                ->body('Please set GEMINI_API_KEY in your .env file.')
                                                ->danger()
                                                ->send();
                                            return;
                                        }

                                        $soilData = [
                                            'soil_type'      => $get('soil_type'),
                                            'crop_variety'   => $get('crop_variety'),
                                            'ph_level'       => $get('ph_level'),
                                            'organic_matter' => $get('organic_matter'),
                                            'nitrogen'       => $get('nitrogen'),
                                            'phosphorus'     => $get('phosphorus'),
                                            'potassium'      => $get('potassium'),
                                        ];

                                        $recommendation = app(GeminiService::class)->generateSoilRecommendation($soilData);
                                        $set('recommendation', $recommendation);

                                        Notification::make()
                                            ->title('AI Recommendation Generated')
                                            ->body('Review and edit the recommendation before saving.')
                                            ->success()
                                            ->send();
                                    }),
                            ]),
                            Forms\Components\Textarea::make('recommendation')
                                ->rows(20)
                                ->autosize()
                                ->extraAttributes(['style' => 'min-height: 400px; font-family: monospace; font-size: 0.85rem;'])
                                ->helperText('AI-generated or manually entered recommendation. Summarize fertilizer rates, timing, and condition-specific advice.'),
                        ]),

                    // ————————————————————— Expert Validation —————————————————————
                    Tab::make('Expert Validation')
                        ->schema([
                            Forms\Components\Select::make('validation_status')
                                ->label('Validation Status')
                                ->options([
                                    'pending' => 'Pending',
                                    'approved' => 'Approved',
                                    'disapproved' => 'Disapproved',
                                ])
                                ->default('pending')
                                ->disabled(),

                            Forms\Components\Textarea::make('expert_comments')
                                ->label('Expert Recommendation/Comments')
                                ->rows(4)
                                ->autosize()
                                ->helperText('Comments or recommendations from the agricultural expert'),

                            Placeholder::make('validated_by_name')
                                ->label('Validated By')
                                ->content(fn ($record) => $record?->validator?->name ?? '—'),
                        ]),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
                        $query->whereHas('farmer', fn ($q) => $q->where('municipality', $professional->municipality));
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
                            ->modalContent(fn (SoilAnalysis $record) => view('filament.resources.soil-analysis.view-modal', ['record' => $record]))
                            ->modalFooterActions(fn (SoilAnalysis $record, Action $action) => [
                                Action::make('recommend')
                                    ->label('Expert Recommendation')
                                    ->icon('heroicon-o-star')
                                    ->color('info')
                                    ->size('lg')
                                    ->extraAttributes(['class' => 'px-8 py-3 mx-2'])
                                    ->form([
                                        Forms\Components\Actions::make([
                                            // ── Static (Guide-Based) draft ──
                                            Forms\Components\Actions\Action::make('generateStaticDraft')
                                                ->label('Guide-Based Draft')
                                                ->icon('heroicon-o-document-text')
                                                ->color('warning')
                                                ->tooltip('For WITH LAB: uses BSWM/FAO soil guidelines. For WITHOUT LAB: uses the KAPE non-bearing coffee guide.')
                                                ->action(function (Forms\Set $set) use ($record) {
                                                    $soilData = [
                                                        'soil_type'      => $record->soil_type,
                                                        'crop_variety'   => $record->crop_variety,
                                                        'ph_level'       => $record->ph_level,
                                                        'organic_matter' => $record->organic_matter,
                                                        'nitrogen'       => $record->nitrogen,
                                                        'phosphorus'     => $record->phosphorus,
                                                        'potassium'      => $record->potassium,
                                                    ];

                                                    $isNoLab = $record->analysis_type === 'without_lab';

                                                    if ($isNoLab) {
                                                        $draft = app(NoLabRecommendationService::class)->generate($soilData);
                                                        $body  = 'Based on KAPE / BSWM non-bearing coffee guide (no laboratory).';
                                                    } else {
                                                        $draft = app(SoilRecommendationService::class)->generate($soilData);
                                                        $body  = 'Based on BSWM/FAO guidelines. Review and edit before submitting.';
                                                    }

                                                    $set('expert_comments', $draft);

                                                    Notification::make()
                                                        ->title('Guide-Based Draft Generated')
                                                        ->body($body)
                                                        ->success()
                                                        ->send();
                                                }),

                                            // ── AI draft (Gemini) ──
                                            Forms\Components\Actions\Action::make('generateAiDraft')
                                                ->label('Generate AI Draft')
                                                ->icon('heroicon-o-sparkles')
                                                ->color('info')
                                                ->tooltip('Uses Google Gemini AI to draft a recommendation based on the soil analysis data. Review and edit before submitting.')
                                                ->disabled(fn () => $record->analysis_type === 'without_lab')
                                                ->action(function (Forms\Set $set) use ($record) {
                                                    if (!config('services.gemini.api_key')) {
                                                        Notification::make()
                                                            ->title('API Key Not Configured')
                                                            ->body('Please set GEMINI_API_KEY in your .env file.')
                                                            ->danger()
                                                            ->send();
                                                        return;
                                                    }

                                                    $draft = app(GeminiService::class)->generateSoilRecommendation([
                                                        'soil_type'      => $record->soil_type,
                                                        'crop_variety'   => $record->crop_variety,
                                                        'ph_level'       => $record->ph_level,
                                                        'organic_matter' => $record->organic_matter,
                                                        'nitrogen'       => $record->nitrogen,
                                                        'phosphorus'     => $record->phosphorus,
                                                        'potassium'      => $record->potassium,
                                                    ]);

                                                    $set('expert_comments', $draft);

                                                    Notification::make()
                                                        ->title('AI Draft Generated')
                                                        ->body('Review and edit the AI draft before submitting your recommendation.')
                                                        ->success()
                                                        ->send();
                                                }),
                                        ]),
                                        Forms\Components\Select::make('validation_status')
                                            ->label('Status')
                                            ->options([
                                                'approved' => 'Approve Analysis',
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
                                            'expert_comments' => $data['expert_comments'],
                                            'validated_by' => Auth::id(),
                                            'validated_at' => now(),
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
