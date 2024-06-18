<?php

namespace App\Jobs;

use App\Models\Prospection;
use App\Services\CnpjWs\CnpjWsService;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class CreateProspectionCompanyJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $backoff = 5;

    public $timeout = 120;

    public function __construct(protected Prospection $prospection, protected string $cnpj)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $service = new CnpjWsService();
        $response = $service->prospections()->getCNPJ($this->cnpj);

        $this->prospection->company()->update([
            'cnpj' => $this->cnpj,
            'cnpj_raiz' => $response['estabelecimento']['cnpj_raiz'],
            'cnpj_ordem' => $response['estabelecimento']['cnpj_ordem'],
            'cnpj_digito_verificador' => $response['estabelecimento']['cnpj_digito_verificador'],
            'tipo' => $response['estabelecimento']['tipo'],
            'nome_fantasia' => $response['estabelecimento']['nome_fantasia'],
            'razao_social' => $response['razao_social'],
            'capital_social' => $response['capital_social'],
            'responsavel_federativo' => $response['responsavel_federativo'],
            'atualizado_em' => Carbon::parse($response['atualizado_em']),
            'porte' => $response['porte'],
            'natureza_juridica' => $response['natureza_juridica'],
            'qualificacao_do_responsavel' => $response['qualificacao_do_responsavel'],
            'socios' => $response['socios'],
            'simples' => $response['simples'],
            'atividades_secundarias' => $response['estabelecimento']['atividades_secundarias'],
            'situacao_cadastral' => $response['estabelecimento']['situacao_cadastral'],
            'data_situacao_cadastral' => $response['estabelecimento']['data_situacao_cadastral'],
            'data_inicio_atividade' => $response['estabelecimento']['data_inicio_atividade'],
            'nome_cidade_exterior' => $response['estabelecimento']['nome_cidade_exterior'],
            'tipo_logradouro' => $response['estabelecimento']['tipo_logradouro'],
            'logradouro' => $response['estabelecimento']['logradouro'],
            'numero' => $response['estabelecimento']['numero'],
            'complemento' => $response['estabelecimento']['complemento'],
            'bairro' => $response['estabelecimento']['bairro'],
            'cep' => $response['estabelecimento']['cep'],
            'ddd1' => $response['estabelecimento']['ddd1'],
            'telefone1' => $response['estabelecimento']['telefone1'],
            'ddd2' => $response['estabelecimento']['ddd2'],
            'telefone2' => $response['estabelecimento']['telefone2'],
            'ddd_fax' => $response['estabelecimento']['ddd_fax'],
            'fax' => $response['estabelecimento']['fax'],
            'email' => $response['estabelecimento']['email'],
            'situacao_especial' => $response['estabelecimento']['situacao_especial'],
            'data_situacao_especial' => $response['estabelecimento']['data_situacao_especial'],
            'atividade_principal' => $response['estabelecimento']['atividade_principal'],
            'pais' => $response['estabelecimento']['pais'],
            'estado' => $response['estabelecimento']['estado'],
            'cidade' => $response['estabelecimento']['cidade'],
            'motivo_situacao_cadastral' => $response['estabelecimento']['motivo_situacao_cadastral'],
            'inscricoes_estaduais' => $response['estabelecimento']['inscricoes_estaduais'],
        ]);
    }

    public function failed(?Throwable $exception): void
    {
        $recipient = auth()->user();

        Notification::make()
            ->title($exception->getMessage())
            ->sendToDatabase($recipient);
    }
}
