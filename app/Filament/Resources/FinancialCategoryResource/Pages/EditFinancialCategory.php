<?php

namespace App\Filament\Resources\FinancialCategoryResource\Pages;

use App\Filament\Resources\FinancialCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFinancialCategory extends EditRecord
{
    protected static string $resource = FinancialCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
