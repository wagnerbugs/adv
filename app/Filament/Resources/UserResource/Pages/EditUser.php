<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use App\Filament\Resources\UserResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        $recipient = auth()->user();

        Notification::make()
            ->title('User updated successfully')
            ->sendToDatabase($recipient);

        Notification::make()
            ->title('User updated successfully')
            ->broadcast($recipient);
    }
}
