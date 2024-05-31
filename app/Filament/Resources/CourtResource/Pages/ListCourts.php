<?php

namespace App\Filament\Resources\CourtResource\Pages;

use App\Filament\Resources\CourtResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCourts extends ListRecords
{
    protected static string $resource = CourtResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
