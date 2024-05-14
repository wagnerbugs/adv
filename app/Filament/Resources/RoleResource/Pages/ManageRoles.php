<?php

namespace App\Filament\Resources\RoleResource\Pages;

use Filament\Actions;
use App\Filament\Resources\RoleResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManageRoles extends ManageRecords
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function beforeCreate(): void
    {
        $recipient = auth()->user();

        Notification::make()
            ->title('Role created successfully')
            ->sendToDatabase($recipient);
    }

    protected function beforeSave(): void
    {
        $recipient = auth()->user();

        Notification::make()
            ->title('Role updated successfully')
            ->sendToDatabase($recipient);
    }
}
