<?php

namespace App\Filament\Resources\ProcessResource\Pages;

use Exception;
use Carbon\Carbon;
use App\Models\Court;
use Filament\Actions;
use App\Helpers\Helper;
use App\Models\Process;
use Filament\Forms\Form;
use Illuminate\Bus\Batch;
use App\Models\CourtState;
use Illuminate\Http\Request;
use App\Models\CourtDistrict;
use App\Models\ProcessDetail;
use App\Models\ProcessSubject;
use App\Events\UpdateMovements;
use App\Models\ProcessMovement;
use App\Jobs\CreateProcessDetail;
use App\Jobs\CreateProcessSubject;

use App\Jobs\CreateProcessMovement;
use App\Traits\ProcessNumberParser;
use Illuminate\Support\Facades\Bus;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ProcessResource;
use App\Services\CNJ\Process\ProcessService;
use App\Services\CNJ\Procedural\ProceduralService;

class CreateProcess extends CreateRecord
{
    use ProcessNumberParser;

    protected static string $resource = ProcessResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function beforeCreate(): void
    {
        $process =  $this->form->getLivewire()->data['process'];
        $number = preg_replace('/[^0-9]/', '', $process);
        $process_parser = $this->processNumberParser($number);

        $court_state = CourtState::where('code', $process_parser['court_state_code'])->first();
        $sigla = strtolower($court_state->court);

        $service = new ProcessService();
        $processes = $service->processes()
            ->getProcess("api_publica_{$sigla}", $number);

        if (isset($processes['error'])) {
            Notification::make()
                ->warning()
                ->title('Não foi possível localizar o processo')
                ->body('Por favor, adicione-o manualmente ou tente novamente mais tarde.')
                ->persistent()
                ->actions([
                    Action::make('create')
                        ->label('Adicionar processo manualmente')
                        ->button()
                        ->url(route('filament.admin.resources.processes.create'), shouldOpenInNewTab: false),
                ])
                ->send();

            $this->halt();
        }
    }

    protected function afterCreate(): void
    {
        $process = $this->record;
        $number = preg_replace('/[^0-9]/', '', $process->process);
        $process_parser = $this->processNumberParser($number);

        $process->update([
            'process_number' => $process_parser['process_number'],
            'process_digit' => $process_parser['process_digit'],
            'process_year' => $process_parser['process_year'],
            'court_code' => $process_parser['court_code'],
            'court_state_code' => $process_parser['court_state_code'],
            'court_district_code' => $process_parser['court_district_code'],
        ]);

        $court_state = CourtState::where('code', $process_parser['court_state_code'])->first();

        $sigla = strtolower($court_state->court);

        $service = new ProcessService();
        $responses = $service->processes()->getProcess("api_publica_{$sigla}", $number);

        if (!is_array($responses) || empty($responses)) {
            throw new Exception('Invalid response format or no results found');
        }

        foreach ($responses as $response) {

            $recipient = auth()->user();
            Notification::make()
                ->title("A inserção do processo {$response->process_api_id} em andamento.")
                ->sendToDatabase($recipient);

            CreateProcessDetail::dispatch($process->id, $response);
        }
    }
}
