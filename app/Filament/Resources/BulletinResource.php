<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Bulletin;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use App\Filament\Resources\BulletinResource\Pages;
use App\Helpers\FirebaseNotification; // ✅ Added this import

class BulletinResource extends Resource
{
    protected static ?string $model = Bulletin::class;
    protected static ?string $navigationIcon = 'heroicon-o-information-circle';
    protected static ?string $navigationLabel = 'Bulletins / Announcements';
    protected static ?string $pluralModelLabel = 'Bulletins';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Bulletin Details')
                ->columns([
                    'md' => 1,
                    'lg' => 1,
                    '2xl' => 1,
                ])
                ->schema([
                    Forms\Components\Hidden::make('created_by')
                        ->default(fn() => auth()->id()),

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
                        ->required()
                        ->rows(4),

                    Forms\Components\Hidden::make('date_posted')
                        ->label('Date Posted'),

                    // ✅ Updated toggle with Firebase Notification
                    Forms\Components\Toggle::make('notification_sent')
                        ->label('Send Notification')
                        ->default(false)
                        ->reactive()
                        ->afterStateUpdated(function (callable $set, $state, $record) {
                            if ($state) {
                                // Automatically set date_posted
                                $set('date_posted', now()->toDateString());

                                // ✅ Send push notification
                                // try {
                                //     FirebaseNotification::send(
                                //         $record->title ?? 'New Announcement',
                                //         $record->content ?? 'A new CAFARM bulletin is available.'
                                //     );
                                // } catch (\Throwable $e) {
                                //     \Log::error('❌ Firebase Notification Error: ' . $e->getMessage());
                                    
                                // }
                                 // ✅ Send notification
                            FirebaseNotification::send(
                                $record->category . ' Update',
                                $record->title
                            );


                            } else {
                                $set('date_posted', null);
                            }
                        }),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('category')
                ->label('Category')
                ->sortable()
                ->badge(),

            Tables\Columns\TextColumn::make('title')
                ->label('Title')
                ->searchable()
                ->limit(50),

            Tables\Columns\TextColumn::make('content')
                ->label('Content')
                ->limit(80)
                ->wrap(),

            Tables\Columns\BooleanColumn::make('notification_sent')
                ->label('Notified'),

            Tables\Columns\TextColumn::make('date_posted')
                ->label('Date Posted')
                ->date('M d, Y')
                ->sortable(),
        ])
        ->filters([])
        // ->actions([
        //     Tables\Actions\EditAction::make(),
        // ])

            ->actions([
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\ViewAction::make(),
                        Tables\Actions\EditAction::make(),
                        Tables\Actions\DeleteAction::make(),
                    ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->tooltip('Actions')
                    ->button()
                    ->color('yellow')
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBulletins::route('/'),
            'edit'  => Pages\EditBulletin::route('/{record}/edit'),
        ];
    }
}
