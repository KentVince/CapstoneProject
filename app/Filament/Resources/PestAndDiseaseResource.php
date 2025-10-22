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





class PestAndDiseaseResource extends Resource
{
    protected static ?string $model = PestAndDisease::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-americas';
    protected static ?string $navigationLabel = 'Records';
    protected static ?string $pluralLabel = 'Records';
    protected static ?string $navigationGroup = 'Pest and Disease';
    protected static ?int $navigationSort = 1;

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
            ImageColumn::make('qr_code')
                ->label('QR Code')
                ->height(50), // Adjust size as needed

            Tables\Columns\TextColumn::make('date_detected')->date(),
            // Tables\Columns\TextColumn::make('farmer.firstname')->label('Farmer'),
            Tables\Columns\TextColumn::make('farm.name')->label('Farm Name'),
           
        
            Tables\Columns\TextColumn::make('type'),
            Tables\Columns\TextColumn::make('name'),
            Tables\Columns\TextColumn::make('severity')->badge(),
        //    Tables\Columns\ImageColumn::make('image_url')->label('Image'),
            Tables\Columns\TextColumn::make('diagnosis_result'),
            Tables\Columns\TextColumn::make('recommended_treatment'),
            Tables\Columns\TextColumn::make('treatment_status'),
        ])
        ->filters([
            //
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
            // Action::make('qr-action')
            // ->fillForm(fn(Model $record) => [
            //     'qr-options' => \LaraZeus\Qr\Facades\Qr::getDefaultOptions(),// or $record->qr-options
            //     'qr-data' => 'https://',// or $record->url
            // ])
            // ->form(\LaraZeus\Qr\Facades\Qr::getFormSchema('qr-data', 'qr-options'))
            // ->action(fn($data) => dd($data)),
            Action::make('Generate QR')
    ->icon('heroicon-o-qr-code')
    ->color('success')
    ->action(function (Model $record) {
        try {
            $folder = 'pest_disease_qr';
            if (!Storage::disk('public')->exists($folder)) {
                Storage::disk('public')->makeDirectory($folder);
            }

            $fileName = "{$folder}/case_{$record->id}.png";

            $builder = new BuilderRegistry();
            $builder = $builder->getBuilder(PngWriter::class);

            $result = $builder
                ->data("Pest/Disease Case ID: {$record->id}")
                ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
                ->size(300)
                ->margin(10)
                ->build();

            Storage::disk('public')->put($fileName, $result->getString());
            $record->update(['qr_code' => $fileName]);

            \Filament\Notifications\Notification::make()
                ->title('QR Code Generated ✅')
                ->success()
                ->body('QR Code for this record has been successfully generated.')
                ->send();
        } catch (\Throwable $th) {
            \Filament\Notifications\Notification::make()
                ->title('QR Code Generation Failed ⚠️')
                ->body($th->getMessage())
                ->danger()
                ->send();
        }
    }),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
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
