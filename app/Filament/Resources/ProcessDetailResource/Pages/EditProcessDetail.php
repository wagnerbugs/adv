<?php

namespace App\Filament\Resources\ProcessDetailResource\Pages;

use App\Filament\Resources\ProcessDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProcessDetail extends EditRecord
{
    protected static string $resource = ProcessDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
