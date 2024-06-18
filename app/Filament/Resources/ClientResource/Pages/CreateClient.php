<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Enums\ClientTypeEnum;
use App\Filament\Resources\ClientResource;
use App\Jobs\CreateClientCompanyJob;
use App\Jobs\CreateClientIndividualJob;
use App\Traits\CapitalizeTrait;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateClient extends CreateRecord
{
    use CapitalizeTrait;

    protected static string $resource = ClientResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return static::getModel()::create($data);
    }

    protected function afterCreate(): void
    {
        $client = $this->record;

        if ($client->type === ClientTypeEnum::COMPANY) {
            $cnpj = preg_replace('/[^0-9]/', '', $client->document);

            $recipient = auth()->user();
            Notification::make()
                ->title('Cadastro de cliente em andamento.')
                ->body("Cadastrando o CNPJ: {$client->document}")
                ->sendToDatabase($recipient);

            CreateClientCompanyJob::dispatch($client, $cnpj);
        } elseif ($client->type === ClientTypeEnum::INDIVIDUAL) {
            $cpf = preg_replace('/[^0-9]/', '', $client->document);

            $recipient = auth()->user();
            Notification::make()
                ->title('Cadastro de cliente em andamento.')
                ->body("Cadastrando o CNPJ: {$client->document}")
                ->sendToDatabase($recipient);

            CreateClientIndividualJob::dispatch($client, $cpf);
        }
    }
}
