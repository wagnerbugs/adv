<?php

namespace App\Filament\Resources\ProspectionResource\Pages;

use App\Filament\Resources\ProspectionResource;
use App\Jobs\CreateProspectionCompanyJob;
use App\Jobs\CreateProspectionIndividualJob;
use App\Jobs\CreateProspectionProcessJob;
use App\Models\CourtState;
use App\Services\CNJ\Process\ProcessService;
use App\Traits\ProcessNumberParser;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateProspection extends CreateRecord
{
    use ProcessNumberParser;

    protected static string $resource = ProspectionResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Prospecção criada com sucesso')
            ->body('Os dados do Lead estarão disponíveis em breve.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function beforeCreate(): void
    {
        $prospection = $this->form->getLivewire()->data['process'];
        if (! empty($prospection)) {
            $number = preg_replace('/[^0-9]/', '', $prospection);
            $process_parser = $this->processNumberParser($number);

            $court_state = CourtState::where('code', $process_parser['court_state_code'])->first();
            $sigla = strtolower($court_state->court);

            $service = new ProcessService();
            $processes = $service->prospections()->getProcess("api_publica_{$sigla}", $number);

            if (isset($processes['error'])) {
                Notification::make()
                    ->warning()
                    ->title('Não foi possível localizar o processo')
                    ->body('Por favor, adicione-o manualmente após a pesquisa.')
                    ->persistent()
                    ->send();
            }
        }
    }

    protected function afterCreate()
    {
        $prospection = $this->record;

        if ($prospection->cnpj !== null) {
            $cnpj = preg_replace('/[^0-9]/', '', $prospection->cnpj);

            $recipient = auth()->user();
            Notification::make()
                ->title('Pesquisa de dados em andamento.')
                ->body("Pesquisando CNPJ: {$prospection->cnpj}")
                ->sendToDatabase($recipient);

            CreateProspectionCompanyJob::dispatch($prospection, $cnpj);
        }

        if ($prospection->cpf !== null) {

            $cpf = preg_replace('/[^0-9]/', '', $prospection->cpf);

            $recipient = auth()->user();
            Notification::make()
                ->title('Pesquisa de dados em andamento.')
                ->body("Pesquisando CPF: {$prospection->cpf}")
                ->sendToDatabase($recipient);

            CreateProspectionIndividualJob::dispatch($prospection, $cpf);
        }

        if ($prospection->process !== null) {
            $process = preg_replace('/[^0-9]/', '', $prospection->process);

            $recipient = auth()->user();
            Notification::make()
                ->title('Pesquisa de dados em andamento.')
                ->body("Pesquisando Processo: {$prospection->process}")
                ->sendToDatabase($recipient);

            CreateProspectionProcessJob::dispatch($prospection->id, $process);
        }
    }
}
