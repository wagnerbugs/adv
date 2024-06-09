<?php

namespace App\Jobs;

use Exception;
use Throwable;
use App\Models\CourtState;
use App\Models\Prospection;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use App\Models\ProspectionProcess;
use App\Traits\ProcessNumberParser;
use App\Services\CnpjWs\CnpjWsService;
use Illuminate\Queue\SerializesModels;
use Filament\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\CNJ\Process\ProcessService;
use App\Services\ApiBrasil\CPF\ApiBrasilCPFService;
use Carbon\Carbon;

class CreateProspectionProcessJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ProcessNumberParser;

    public $tries = 5;
    public $backoff = 5;
    public $timeout = 120;

    public function __construct(protected $prospectionID, protected string $process)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $process_parser = $this->processNumberParser($this->process);
        $court_state = CourtState::where('code', $process_parser['court_state_code'])->first();
        $sigla = strtolower($court_state->court);

        $service = new ProcessService();
        $responses = $service->prospections()->getProcess("api_publica_{$sigla}", $this->process);

        if (!is_array($responses) || empty($responses)) {
            $recipient = auth()->user();
            Notification::make()
                ->title("Nenhum processo encontrado")
                ->body("Adicionar manualmente o processo: {$this->process}")
                ->sendToDatabase($recipient);
        }

        foreach ($responses as $response) {

            $source = $response['_source'];

            ProspectionProcess::create([
                'prospection_id' => $this->prospectionID,
                'process' => $this->process,
                'process_number' => $process_parser['process_number'],
                'process_digit' => $process_parser['process_digit'],
                'process_year' => $process_parser['process_year'],
                'court_code' => $process_parser['court_code'],
                'court_state_code' => $process_parser['court_state_code'],
                'court_district_code' => $process_parser['court_district_code'],
                'classe' => $source['classe'],
                'sistema' => $source['sistema'],
                'formato' => $source['formato'],
                'tribunal' => $source['tribunal'],
                'dataHoraUltimaAtualizacao' => Carbon::parse($source['dataHoraUltimaAtualizacao']),
                'grau' => $source['grau'],
                'dataAjuizamento' => Carbon::parse($source['dataAjuizamento']),
                'movimentos' => $source['movimentos'],
                'process_api_id' => $source['id'],
                'nivelSigilo' => $source['nivelSigilo'],
                'orgaoJulgador' => $source['orgaoJulgador'],
                'assuntos' => $source['assuntos'],
            ]);
        }
    }

    public function failed(?Throwable $exception): void
    {
        $recipient = auth()->user();

        Notification::make()
            ->title($exception->getMessage())
            ->sendToDatabase($recipient);
    }
}
