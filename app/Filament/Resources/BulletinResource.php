<?php

namespace App\Filament\Resources;

use App\Models\Bulletin;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use App\Filament\Resources\BulletinResource\Pages;
use Illuminate\Support\Facades\Auth;

class BulletinResource extends Resource
{
    protected static ?string $model = Bulletin::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationLabel = 'Bulletins';
    protected static ?string $pluralModelLabel = 'Bulletins';
    protected static ?string $navigationGroup = 'Information Center';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Bulletin Details')
                ->description('Create or update public announcements, events, or notices.')
                ->schema([
                    TextInput::make('title')
                        ->label('Title')
                        ->required()
                        ->maxLength(255),

                    Select::make('category')
                        ->label('Category')
                        ->options([
                            'Announcement' => 'Announcement',
                            'Event' => 'Event',
                            'Notice' => 'Notice',
                        ])
                        ->default('Announcement')
                        ->required(),

                    DatePicker::make('date_posted')
                        ->label('Date Posted')
                        ->default(now())
                        ->required(),

                    RichEditor::make('content')
                        ->label('Content')
                        ->required()
                        ->toolbarButtons([
                            'bold', 'italic', 'underline', 'bulletList',
                            'orderedList', 'link', 'undo', 'redo',
                        ]),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('title')
                ->label('Title')
                ->searchable()
                ->sortable()
                ->limit(40)
                ->wrap(),

            Tables\Columns\TextColumn::make('category')
                ->label('Category')
                ->badge()
                ->colors([
                    'success' => 'Event',
                    'warning' => 'Notice',
                    'info' => 'Announcement',
                ]),

            Tables\Columns\TextColumn::make('date_posted')
                ->label('Posted On')
                ->date('Y-m-d')
                ->sortable(),

            Tables\Columns\TextColumn::make('created_by')
                ->label('Posted By')
                ->formatStateUsing(fn ($state) => $state ?? 'Admin')
                ->sortable(),
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
            Tables\Actions\DeleteBulkAction::make(),
        ])
        ->defaultSort('date_posted', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBulletins::route('/'),
            'create' => Pages\CreateBulletin::route('/create'),
            'edit' => Pages\EditBulletin::route('/{record}/edit'),
        ];
    }

    /**
     * 🧠 Hook before save to auto-fill author and send notification flag
     */
    public static function beforeSave(Bulletin $record): void
    {
        if (! $record->created_by) {
            $record->created_by = Auth::user()->name ?? 'System';
        }

        // Reset notification flag to false (for mobile push later)
        $record->notification_sent = false;
    }

    /**
     * 📢 After saving, show success toast in Filament
     */
    public static function afterSave(Bulletin $record): void
    {
        Notification::make()
            ->title('Bulletin Saved Successfully ✅')
            ->body("Bulletin <b>{$record->title}</b> has been posted.")
            ->success()
            ->send();
    }
}
