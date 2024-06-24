<?php

namespace App\Filament\Resources\FinancialCategoryResource\Pages;

use App\Filament\Resources\FinancialCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFinancialCategories extends ListRecords
{
    protected static string $resource = FinancialCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
