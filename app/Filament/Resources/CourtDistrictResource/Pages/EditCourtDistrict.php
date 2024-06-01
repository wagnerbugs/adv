<?php

namespace App\Filament\Resources\CourtDistrictResource\Pages;

use App\Filament\Resources\CourtDistrictResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCourtDistrict extends EditRecord
{
    protected static string $resource = CourtDistrictResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
