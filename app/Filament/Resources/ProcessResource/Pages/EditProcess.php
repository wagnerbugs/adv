<?php

namespace App\Filament\Resources\ProcessResource\Pages;

use Filament\Actions;
use App\Models\Process;
use App\Models\ProcessChat;
use App\Models\ProcessDetail;
use App\Models\ProcessAgreement;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\FileUpload;
use App\Filament\Resources\ProcessResource;
use Leandrocfe\FilamentPtbrFormFields\Money;

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

                    FileUpload::make('files')
                        ->label('Anexo')
                        ->multiple()
                        ->previewable()
                        ->downloadable()
                        ->openable()
                        ->directory('process/chats/files'),
                ]),

            Actions\CreateAction::make('add_agreement')
                ->label('Adicionar Acordo')
                ->color('warning')
                ->model(ProcessAgreement::class)
                ->form([
                    Select::make('processes')
                        ->multiple()
                        ->options(Process::whereJsonContains('clients', $this->record->clients)->get()->pluck('process', 'id'))
                        ->required(),
                    Hidden::make('user_id')
                        ->default(auth()->user()->id)
                        ->required(),

                    Fieldset::make('Outra parte')
                        ->schema([
                            TextInput::make('dealer')
                                ->label('Negociador'),

                            TextInput::make('company')
                                ->label('Empresa'),

                            Repeater::make('phones')
                                ->label('Telefones')
                                ->schema([
                                    TextInput::make('phone')
                                        ->label('Telefone'),
                                ]),

                            Repeater::make('emails')
                                ->label('E-mails')
                                ->schema([
                                    TextInput::make('email')
                                        ->label('E-mail')
                                        ->email(),
                                ]),

                        ]),

                    Textarea::make('notes')
                        ->label('Observações')
                        ->columnSpanFull(),

                    Money::make('amount')
                        ->label('Valor'),

                ]),
            Actions\DeleteAction::make(),
        ];
    }
}
