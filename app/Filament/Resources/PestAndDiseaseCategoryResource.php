<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\PestAndDiseaseCategory;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PestAndDiseaseCategoryResource\Pages;
use App\Filament\Resources\PestAndDiseaseCategoryResource\RelationManagers;

class PestAndDiseaseCategoryResource extends Resource
{
    protected static ?string $model = PestAndDiseaseCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Pest and Disease';
    protected static ?string $navigationLabel = 'Categories';
    protected static ?string $pluralLabel = 'Categories';
    protected static ?int $navigationSort = 1; // Optional: Set order within the group
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //

                Section::make()
                ->columns([
                    'md' => 1,  // normal medium monitor
                    'lg' => 2,  // my small monitor
                    '2xl' => 2, // my large monitor
                ])
            ->schema([

                Forms\Components\Select::make('type')
                ->label('Category')
                ->options([
                    'Pest' => 'Pest',
                    'Disease' => 'Disease',
                ]) // Allows users to select Pest or Disease
                ->required(),
            Forms\Components\TextInput::make('name')
                ->label('Name')
                ->required(),
            Forms\Components\Textarea::make('description')
                ->label('Description')
                ->nullable(),

                ])


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\BadgeColumn::make('type')
                ->label('Category')
                ->colors([
                    'success' => 'Pest', // Green for Pest
                    'danger' => 'Disease', // Red for Disease
                ]), // Add colors for visual distinction
            Tables\Columns\TextColumn::make('name')
                ->label('Name')
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('description')
                ->label('Description')
                ->limit(50),
            ])
            ->filters([
                //
            ])
            ->actions([
                  Tables\Actions\ActionGroup::make([
                     
                        Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPestAndDiseaseCategories::route('/'),
          //  'create' => Pages\CreatePestAndDiseaseCategory::route('/create'),
            'edit' => Pages\EditPestAndDiseaseCategory::route('/{record}/edit'),
        ];
    }
}
