<?php

namespace App\Filament\Resources\ProcessResource\Pages;

use Filament\Actions;
use App\Models\ProcessChat;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ProcessResource;

class EditProcess extends EditRecord
{
    protected static string $resource = ProcessResource::class;

    protected function getHeaderActions(): array
    {
        return [

            Actions\CreateAction::make('add_history')
                ->label('Adicionar Histórico')
                ->model(ProcessChat::class)
                ->form([
                    Hidden::make('process_id')
                        ->default($this->record->id)
                        ->required(),
                    Hidden::make('user_id')
                        ->default(auth()->user()->id)
                        ->required(),

                    TextInput::make('message')
                        ->label('Histórico')
                        ->required(),
                ]),
            Actions\DeleteAction::make(),
        ];
    }
}
