<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Bulletin;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BulletinResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BulletinResource\RelationManagers;

class BulletinResource extends Resource
{
    protected static ?string $model = Bulletin::class;

    protected static ?string $navigationIcon = 'heroicon-o-information-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make()
                ->columns([
                    'md' => 1,  // normal medium monitor
                    'lg' => 1,  // my small monitor
                    '2xl' => 1, // my large monitor
                ])
            ->schema([
                Forms\Components\Hidden::make('created_by')->default(fn () => auth()->id()), // Automatically set to the current user ID,
                // Forms\Components\DatePicker::make('date_posted')
                //     ->label('Date Posted')
                //     ->required(),
                Forms\Components\Select::make('category')
                    ->label('Category')
                    ->options([
                        'Announcement' => 'Announcement',
                        'Event' => 'Event',
                        'Notice' => 'Notice',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->label('Title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('content')
                    ->label('Content')
                    ->required(),
                    Forms\Components\Hidden::make('date_posted')
                    ->label('Date Posted'),
                    
                    Forms\Components\Toggle::make('notification_sent')
                    ->label('Notification Sent')
                    ->default(false)
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state) {
                        if ($state) {
                            // Automatically set date_posted to the current date
                            $set('date_posted', now()->toDateString());
                        } else {
                            // Clear the date_posted field if notification_sent is set to false
                            $set('date_posted', null);
                        }
                    }),
                   

                ])
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('created_by')
                // ->label('Created By')
                // ->sortable()
                // ->searchable(),
                Tables\Columns\TextColumn::make('category')
                ->label('Category')
                ->sortable()
                ->badge(),

                Tables\Columns\TextColumn::make('title')
                ->label('Title')
                ->searchable(),

            Tables\Columns\TextColumn::make('content')
                ->label('Content')
                ->sortable(),
           
           
            Tables\Columns\BooleanColumn::make('notification_sent')
                ->label('Notification Sent'),


            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
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
            'index' => Pages\ListBulletins::route('/'),
        //    'create' => Pages\CreateBulletin::route('/create'),
            'edit' => Pages\EditBulletin::route('/{record}/edit'),
        ];
    }
}
