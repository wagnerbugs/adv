<?php

namespace App\Filament\Resources\ProspectionResource\Pages;

use App\Filament\Resources\ProspectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProspections extends ListRecords
{
    protected static string $resource = ProspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
