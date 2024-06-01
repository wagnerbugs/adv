<?php

namespace App\Filament\Resources\CourtStateResource\Pages;

use App\Filament\Resources\CourtStateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCourtState extends EditRecord
{
    protected static string $resource = CourtStateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
