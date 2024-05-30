<?php

namespace App\Filament\Resources\ClientCompanyResource\Pages;

use App\Filament\Resources\ClientCompanyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClientCompanies extends ListRecords
{
    protected static string $resource = ClientCompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Cadastrar Cliente'),
        ];
    }
}
