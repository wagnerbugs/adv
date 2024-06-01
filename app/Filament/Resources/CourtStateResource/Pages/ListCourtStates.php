<?php

namespace App\Filament\Resources\CourtStateResource\Pages;

use App\Filament\Resources\CourtStateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCourtStates extends ListRecords
{
    protected static string $resource = CourtStateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
