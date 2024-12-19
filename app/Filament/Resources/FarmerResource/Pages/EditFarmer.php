<?php

namespace App\Filament\Resources\FarmerResource\Pages;

use App\Filament\Resources\FarmerResource;
use App\Models\Farmer;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFarmer extends EditRecord
{
    protected static string $resource = FarmerResource::class;

    protected function getHeaderActions(): array
    {
        return [
          //  Actions\DeleteAction::make(),
        ];
    }

    public function mount(int|string $record): void
    {
      $this->record = Farmer::findOrFail($record)->load('farm');

      $farm = $this->record->farm;

      $record = collect($this->record)->except('farm')->merge($farm->toArray());

      $this->form->fill($record->toArray());
    }
}
