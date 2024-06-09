<?php

namespace App\Filament\Resources\ProcessDetailResource\Pages;

use Filament\Actions;
use App\Models\ProcessDetail;
use App\Models\ProcessDetailChat;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ProcessDetailResource;

class EditProcessDetail extends EditRecord
{
    protected static string $resource = ProcessDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
            Actions\CreateAction::make('add_history')
                ->label('Adicionar Histórico')
                ->model(ProcessDetailChat::class)
                ->form([
                    Hidden::make('process_detail_id')
                        ->default($this->record->id)
                        ->required(),
                    Hidden::make('user_id')
                        ->default(auth()->user()->id)
                        ->required(),

                    TextInput::make('message')
                        ->label('Histórico')
                        ->required(),
                ]),
        ];
    }
}
