<?php

namespace App\Filament\Resources\ProcessChatResource\Pages;

use App\Filament\Resources\ProcessChatResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProcessChat extends EditRecord
{
    protected static string $resource = ProcessChatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
