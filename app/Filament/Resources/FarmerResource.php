<?php

namespace App\Filament\Resources;


use Filament\Forms;
use Filament\Tables;
use App\Models\Farmer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Section;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Pages\Componets\FarmerForm;
use App\Filament\Resources\FarmerResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\FarmerResource\RelationManagers;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class FarmerResource extends Resource implements HasShieldPermissions
{
    use HasRoles;
    protected static ?string $model = Farmer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'Farmer Information';
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
            // 'publish'
        ];
    }


    public static function form(Form $form): Form
{

    $farmerForm = new FarmerForm($form->getModelInstance()); // Pass the model/record

        return $form
            ->schema([
                Section::make()
                    ->columns([
                        'sm' => 2,
                        'm' => 1,
                        'lg' => 1,
                        '2xl' => 1,
                        'screen' => 1,
                    ])
                    ->schema($farmerForm->getAccountSchema()), // Get wizard schema for PDS entry
            ]);


}




    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->when(!auth()->user()->hasRole('super_admin'), fn($q) => $q->where('user_id', auth()->id())))
            ->columns([
                //
                Tables\Columns\TextColumn::make('app_no')
                ->searchable(),
                Tables\Columns\TextColumn::make('crop')
                ->label('PO No.')
                ->searchable(),
                Tables\Columns\TextColumn::make('funding_source')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lastname')
                    ->searchable(),

                    Tables\Columns\TextColumn::make('firstname')

                    ->searchable(),

                Tables\Columns\TextColumn::make('middlename')
                    ->searchable(),


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

           'index2' => Pages\ListFarmers::route('/index2'),
           'index' => Pages\RedirectToFarmerPage::route('/'),
          //  'editfarmer' => Pages\RedirectToFarmerPage::route('/'),
            'create' => Pages\CreateFarmer::route('/create'),
            'edit' => Pages\EditFarmer::route('/{record}/edit'),


        ];
    }
}
