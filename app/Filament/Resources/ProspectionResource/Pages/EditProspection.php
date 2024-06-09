<?php

namespace App\Filament\Resources\ProspectionResource\Pages;

use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ProspectionResource;
use App\Traits\CapitalizeTrait;

class EditProspection extends EditRecord
{
    use CapitalizeTrait;

    protected static string $resource = ProspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
