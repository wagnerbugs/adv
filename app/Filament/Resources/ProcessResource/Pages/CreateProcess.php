<?php

namespace App\Filament\Resources\ProcessResource\Pages;

use Carbon\Carbon;
use App\Models\Court;
use Filament\Actions;
use App\Helpers\Helper;
use App\Models\Process;
use Filament\Forms\Form;
use App\Models\CourtState;
use Illuminate\Http\Request;
use App\Models\CourtDistrict;
use App\Traits\ProcessNumberParser;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ProcessResource;
use App\Services\CNJ\Process\ProcessService;
use App\Services\CNJ\Procedural\ProceduralService;

class CreateProcess extends CreateRecord
{
    use ProcessNumberParser;

    protected static string $resource = ProcessResource::class;

    protected function afterCreate(): void
    {
        $process =  $this->record;
        $number = preg_replace('/[^0-9]/', '', $process->process);
        $process_parser = $this->processNumberParser($number);

        $court_state = CourtState::where('code', $process_parser['court_state_code'])->first();
        $sigla = strtolower($court_state->court);

        $service = new ProcessService();
        $response = $service->processes()
            ->getProcess("api_publica_{$sigla}", $number);

        $class_code = $response['hits']['hits'][0]['_source']['classe']['codigo'];
        $class_name = $response['hits']['hits'][0]['_source']['classe']['nome'];
        $publish_date = $response['hits']['hits'][0]['_source']['dataAjuizamento'];
        $last_modification_date = $response['hits']['hits'][0]['_source']['dataHoraUltimaAtualizacao'];
        $secrecy_level = $response['hits']['hits'][0]['_source']['nivelSigilo'];
        $movements = $response['hits']['hits'][0]['_source']['movimentos'];
        $subjects = $response['hits']['hits'][0]['_source']['assuntos'];

        foreach ($movements as $movement) {
            $complements = isset($movement['complementosTabelados']) ? $movement['complementosTabelados'] : null;
            $code = $movement['codigo'];
            $proceduralService = new ProceduralService();
            $procedural_response = $proceduralService->movements()->get($code);

            $process->movements()->create([
                'code' => $movement['codigo'],
                'name' => $movement['nome'],
                'description' => Helper::cleanText($procedural_response[0]['glossario']),
                'date' => Carbon::parse($movement['dataHora']),
                'complements' => $complements !== null ? json_encode($complements) : null,
            ]);
        }

        foreach ($subjects as $subject) {

            $proceduralService = new ProceduralService();
            $procedural_response = $proceduralService->subjects()->get($subject['codigo']);

            $process->subjects()->create([
                'code' => $subject['codigo'],
                'name' => $subject['nome'],
                'description' => Helper::cleanText($procedural_response[0]['descricao_glossario']),
                'rule' => $procedural_response[0]['norma'],
                'article' => $procedural_response[0]['artigo'],
            ]);
        }

        $proceduralService = new ProceduralService();
        $procedural_response = $proceduralService->classes()->get($class_code);

        $class_description = $procedural_response[0]['descricao_glossario'];
        $nature = $procedural_response[0]['natureza'];
        $active_pole = $procedural_response[0]['polo_ativo'];
        $npassive_poleture = $procedural_response[0]['polo_passivo'];
        $rule = $procedural_response[0]['norma'];
        $article = $procedural_response[0]['artigo'];

        $process->update([
            'process_number' => $process_parser['process_number'],
            'process_digit' => $process_parser['process_digit'],
            'process_year' => $process_parser['process_year'],
            'court_code' => $process_parser['court_code'],
            'court_state_code' => $process_parser['court_state_code'],
            'court_disctric_code' => $process_parser['court_disctric_code'],
            'class_code' => $class_code,
            'class_name' => $class_name,
            'class_description' => Helper::cleanText($class_description),
            'nature' => $nature,
            'active_pole' => $active_pole,
            'passive_pole' => $npassive_poleture,
            'rule' => $rule,
            'article' => $article,
            'publish_date' => Carbon::parse($publish_date),
            'last_modification_date' => Carbon::parse($last_modification_date),
            'secrecy_level' => $secrecy_level,
            'movements' => json_encode($movements),
            'subjects' => json_encode($subjects),
        ]);
    }
}
