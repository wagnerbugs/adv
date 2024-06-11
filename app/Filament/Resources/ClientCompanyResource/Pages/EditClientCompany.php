<?php

namespace App\Filament\Resources\ClientCompanyResource\Pages;

use App\Filament\Resources\ClientCompanyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClientCompany extends EditRecord
{
    protected static string $resource = ClientCompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
