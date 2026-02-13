<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\PestAndDisease;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\ViewField;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PestAndDiseaseResource\Pages;
use App\Filament\Resources\PestAndDiseaseResource\RelationManagers;

use Endroid\QrCode\Builder\BuilderRegistry;
use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;


use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Infolist;





class PestAndDiseaseResource extends Resource
{
    protected static ?string $model = PestAndDisease::class;

    protected static ?string $navigationIcon = 'heroicon-o-bug-ant';
    protected static ?string $navigationLabel = 'Records';
    protected static ?string $pluralLabel = 'Records';
    protected static ?string $navigationGroup = 'Pest and Disease';
    protected static ?int $navigationSort = 1;

    /**
     * Get the navigation badge showing count of pending detections
     */
    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('validation_status', 'pending')->count();

        return $count > 0 ? (string) $count : null;
    }

    /**
     * Set the badge color to warning (orange) for pending items
     */
    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Section::make()
                ->columns([
                    'md' => 1,
                    'lg' => 2,
                    '2xl' => 2,
                ])
                ->schema([
                    // ViewField::make('qr_code_preview')
                    // ->view('components.qr-code-preview', [
                    //     'record' => fn ($get) => \App\Models\PestAndDisease::find($get('case_id')),
                    // ])
                    // ->label('QR Code Preview'),


                    //                 ViewField::make('qr_code_preview')
                    // ->view('components.qr-code-preview', [
                    //     'record' => fn ($record) => $record, // Pass the entire record directly
                    // ])
                    // ->label('QR Code Preview'),

                 

            //     ViewField::make('qr_code_preview')
            // ->view('components.qr-code', [
            //     'url' => fn ($record) => $record && $record->qr_code
            //         ? Storage::disk('public')->url($record->qr_code)
            //         : null,
            // ])
            // ->label('QR Code Preview')
            // ,

         

                    // Other form fields...
                    Forms\Components\Select::make('farm_id')
                        ->relationship('farm', 'name')
                        ->label('Farm')
                        ->reactive()
                        ->afterStateUpdated(function (callable $set, $state) {
                            if ($state) {
                                $farmerId = \App\Models\Farm::find($state)?->farmer_id;
                                $set('farmer_id', $farmerId);
                            } else {
                                $set('farmer_id', null);
                            }
                        })
                        ->required(),

                    Forms\Components\Hidden::make('farmer_id')->label('Farmer ID'),

                    Forms\Components\Select::make('type')
                        ->label('Type')
                        ->options([
                            'Pest' => 'Pest',
                            'Disease' => 'Disease',
                        ])
                        ->reactive()
                        ->required(),

                    Forms\Components\Select::make('name')
                        ->label('Name')
                        ->options(function (callable $get) {
                            $categoryType = $get('type');
                            if ($categoryType) {
                                return \App\Models\PestAndDiseaseCategory::where('type', $categoryType)
                                    ->pluck('name', 'name');
                            }
                            return [];
                        })
                        ->required()
                        ->reactive()
                        ->disabled(fn (callable $get) => !$get('type')),

                    Forms\Components\Select::make('severity')
                        ->options([
                            'Low' => 'Low',
                            'Medium' => 'Medium',
                            'High' => 'High',
                        ])
                        ->required(),

                    Forms\Components\DatePicker::make('date_detected')->required(),

                    Forms\Components\TextInput::make('diagnosis_result')->required(),
                    Forms\Components\TextInput::make('recommended_treatment')->required(),
                    Forms\Components\TextInput::make('treatment_status')->required(),
                ]),
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            // ImageColumn::make('qr_code')
            //     ->label('QR Code')
            //     ->height(50), // Adjust size as needed

            Tables\Columns\ImageColumn::make('image_path')
                ->label('Image')
                ->disk('public')
                ->height(60)
                ->width(60)
                ->square()
                ->checkFileExistence(false)
                ->extraImgAttributes(['class' => 'rounded-lg object-cover cursor-pointer hover:opacity-75 transition-opacity'])
                ->action(
                    Action::make('viewFromImage')
                        ->modalHeading(fn (PestAndDisease $record) => "Detection: {$record->pest}")
                        ->modalWidth('3xl')
                        ->extraModalWindowAttributes(['class' => 'p-2'])
                        ->modalContent(fn (PestAndDisease $record) => view('filament.resources.pest-and-disease.view-modal', ['record' => $record]))
                        ->modalFooterActions(fn (PestAndDisease $record, Action $action) => [
                            Action::make('approve')
                                ->label('Approve')
                                ->icon('heroicon-o-check-circle')
                                ->color('success')
                                ->size('lg')
                                ->extraAttributes(['class' => 'px-8 py-3 mx-2'])
                                ->requiresConfirmation()
                                ->modalHeading('Approve Detection')
                                ->modalDescription('Are you sure you want to approve this detection?')
                                ->action(function () use ($record, $action) {
                                    $record->update([
                                        'validation_status' => 'approved',
                                        'expert_comments' => null,
                                        'validated_by' => Auth::id(),
                                        'validated_at' => now(),
                                    ]);


                                    Notification::make()
                                        ->title('Detection Approved')
                                        ->success()
                                        ->send();
                                    $action->cancel();
                                })
                                ->visible(fn () => $record->validation_status !== 'approved'),

                            Action::make('disapprove')
                                ->label('Disapprove')
                                ->icon('heroicon-o-x-circle')
                                ->color('danger')
                                ->size('lg')
                                ->extraAttributes(['class' => 'px-8 py-3 mx-2'])
                                ->form([
                                    Textarea::make('expert_comments')
                                        ->label('Comments (Required)')
                                        ->placeholder('Please provide reason for disapproval...')
                                        ->required()
                                        ->rows(3),
                                ])
                                ->action(function (array $data) use ($record, $action) {
                                    $record->update([
                                        'validation_status' => 'disapproved',
                                        'expert_comments' => $data['expert_comments'],
                                        'validated_by' => Auth::id(),
                                        'validated_at' => now(),
                                    ]);


                                    Notification::make()
                                        ->title('Detection Disapproved')
                                        ->body('Comments have been saved.')
                                        ->warning()
                                        ->send();
                                    $action->cancel();
                                })
                                ->visible(fn () => $record->validation_status !== 'disapproved'),
                        ])
                        ->modalFooterActionsAlignment(\Filament\Support\Enums\Alignment::Center)
                ),

            Tables\Columns\TextColumn::make('date_detected')->date(),
            Tables\Columns\TextColumn::make('pest'),
            Tables\Columns\TextColumn::make('severity')->badge(),
            Tables\Columns\TextColumn::make('type')
                ->label('Type')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'pest' => 'danger',
                    'disease' => 'warning',
                    default => 'gray',
                }),
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
        ->filters([
            Tables\Filters\SelectFilter::make('validation_status')
                ->label('Status')
                ->options([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'disapproved' => 'Disapproved',
                ]),
        ])
        ->actions([
            Tables\Actions\ActionGroup::make([
                Action::make('view')
                    ->label('View Details')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading(fn (PestAndDisease $record) => "Detection: {$record->pest}")
                    ->modalWidth('3xl')
                    ->extraModalWindowAttributes(['class' => 'p-2'])
                    ->modalContent(fn (PestAndDisease $record) => view('filament.resources.pest-and-disease.view-modal', ['record' => $record]))
                    ->modalFooterActions(fn (PestAndDisease $record, Action $action) => [
                        Action::make('approve')
                            ->label('Approve')
                            ->icon('heroicon-o-check-circle')
                            ->color('success')
                            ->size('lg')
                            ->extraAttributes(['class' => 'px-8 py-3 mx-2'])
                            ->requiresConfirmation()
                            ->modalHeading('Approve Detection')
                            ->modalDescription('Are you sure you want to approve this detection?')
                            ->action(function () use ($record, $action) {
                                $record->update([
                                    'validation_status' => 'approved',
                                    'expert_comments' => null,
                                    'validated_by' => Auth::id(),
                                    'validated_at' => now(),
                                ]);


                                Notification::make()
                                    ->title('Detection Approved')
                                    ->success()
                                    ->send();
                                $action->cancel();
                            })
                            ->visible(fn () => $record->validation_status !== 'approved'),

                        Action::make('disapprove')
                            ->label('Disapprove')
                            ->icon('heroicon-o-x-circle')
                            ->color('danger')
                            ->size('lg')
                            ->extraAttributes(['class' => 'px-8 py-3 mx-2'])
                            ->form([
                                Textarea::make('expert_comments')
                                    ->label('Comments (Required)')
                                    ->placeholder('Please provide reason for disapproval...')
                                    ->required()
                                    ->rows(3),
                            ])
                            ->action(function (array $data) use ($record, $action) {
                                $record->update([
                                    'validation_status' => 'disapproved',
                                    'expert_comments' => $data['expert_comments'],
                                    'validated_by' => Auth::id(),
                                    'validated_at' => now(),
                                ]);


                                Notification::make()
                                    ->title('Detection Disapproved')
                                    ->body('Comments have been saved.')
                                    ->warning()
                                    ->send();
                                $action->cancel();
                            })
                            ->visible(fn () => $record->validation_status !== 'disapproved'),
                    ])
                    ->modalFooterActionsAlignment(\Filament\Support\Enums\Alignment::Center),
                Tables\Actions\DeleteAction::make(),
            ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Actions')
                ->button()
                ->color('gray')
                ->label('')
          
            // Action::make('qr-action')
            // ->fillForm(fn(Model $record) => [
            //     'qr-options' => \LaraZeus\Qr\Facades\Qr::getDefaultOptions(),// or $record->qr-options
            //     'qr-data' => 'https://',// or $record->url
            // ])
            // ->form(\LaraZeus\Qr\Facades\Qr::getFormSchema('qr-data', 'qr-options'))
            // ->action(fn($data) => dd($data)),
    //         Action::make('Generate QR')
    // ->icon('heroicon-o-qr-code')
    // ->color('success')
    // ->action(function (Model $record) {
    //     try {
    //         $folder = 'pest_disease_qr';
    //         if (!Storage::disk('public')->exists($folder)) {
    //             Storage::disk('public')->makeDirectory($folder);
    //         }

    //         $fileName = "{$folder}/case_{$record->id}.png";

    //         $builder = new BuilderRegistry();
    //         $builder = $builder->getBuilder(PngWriter::class);

    //         $result = $builder
    //             ->data("Pest/Disease Case ID: {$record->id}")
    //             ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
    //             ->size(300)
    //             ->margin(10)
    //             ->build();

    //         Storage::disk('public')->put($fileName, $result->getString());
    //         $record->update(['qr_code' => $fileName]);

    //         \Filament\Notifications\Notification::make()
    //             ->title('QR Code Generated ✅')
    //             ->success()
    //             ->body('QR Code for this record has been successfully generated.')
    //             ->send();
    //     } catch (\Throwable $th) {
    //         \Filament\Notifications\Notification::make()
    //             ->title('QR Code Generation Failed ⚠️')
    //             ->body($th->getMessage())
    //             ->danger()
    //             ->send();
    //     }
    // }),
        ])
          ->actionsColumnLabel('Action') // ✅ this adds the header label
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ])
        ->poll('10s') // Auto-refresh every 10 seconds when Flutter syncs new data
        ->defaultSort('date_detected', 'desc');
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
            'index' => Pages\ListPestAndDiseases::route('/'),
          //  'create' => Pages\CreatePestAndDisease::route('/create'),
            'edit' => Pages\EditPestAndDisease::route('/{record}/edit'),
        ];
    }
}
