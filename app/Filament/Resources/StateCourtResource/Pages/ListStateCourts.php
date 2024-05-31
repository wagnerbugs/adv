<?php

namespace App\Filament\Resources\StateCourtResource\Pages;

use App\Filament\Resources\StateCourtResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStateCourts extends ListRecords
{
    protected static string $resource = StateCourtResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
