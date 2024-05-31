<?php

namespace App\Filament\Resources\OccupationFamilyResource\Pages;

use App\Filament\Resources\OccupationFamilyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOccupationFamily extends EditRecord
{
    protected static string $resource = OccupationFamilyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
