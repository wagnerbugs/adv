<?php

namespace App\Filament\Resources\ProspectionResource\Pages;

use App\Filament\Resources\ProspectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProspection extends EditRecord
{
    protected static string $resource = ProspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
