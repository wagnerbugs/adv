<?php

namespace App\Filament\Resources\ProcessChatResource\Pages;

use App\Filament\Resources\ProcessChatResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProcessChats extends ListRecords
{
    protected static string $resource = ProcessChatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
