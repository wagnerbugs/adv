<?php

namespace App\Filament\Resources\ClientIndividualResource\Pages;

use App\Filament\Resources\ClientIndividualResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClientIndividuals extends ListRecords
{
    protected static string $resource = ClientIndividualResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Cadastrar Cliente'),
        ];
    }
}
