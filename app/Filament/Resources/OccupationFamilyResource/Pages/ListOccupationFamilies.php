<?php

namespace App\Filament\Resources\OccupationFamilyResource\Pages;

use App\Filament\Resources\OccupationFamilyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOccupationFamilies extends ListRecords
{
    protected static string $resource = OccupationFamilyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
