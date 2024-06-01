<?php

namespace App\Filament\Resources\CourtDistrictResource\Pages;

use App\Filament\Resources\CourtDistrictResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCourtDistricts extends ListRecords
{
    protected static string $resource = CourtDistrictResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
