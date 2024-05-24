<?php

namespace App\Filament\Resources\ClientIndividualResource\Pages;

use App\Filament\Resources\ClientIndividualResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClientIndividual extends EditRecord
{
    protected static string $resource = ClientIndividualResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
