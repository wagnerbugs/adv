<?php

namespace App\Jobs;

use App\Models\Client;
use App\Services\CnpjWs\CnpjWsService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateClientCompanyJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $backoff = 5;

    public $timeout = 120;

    public function __construct(protected Client $client, protected string $document)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $service = new CnpjWsService();
        $response = $service->prospections()->getCNPJ($this->document);

        $this->client->company()->update([
            'company' => $response['razao_social'],
            'fantasy_name' => $response['estabelecimento']['nome_fantasia'],
            'share_capital' => $response['capital_social'],
            'company_size' => $response['porte']['descricao'],
            'legal_nature' => $response['natureza_juridica']['descricao'],
            'type' => $response['estabelecimento']['tipo'],
            'registration_status' => $response['estabelecimento']['situacao_cadastral'],
            'registration_date' => $response['estabelecimento']['data_situacao_cadastral'],
            'activity_start_date' => $response['estabelecimento']['data_inicio_atividade'],
            'main_activity' => $response['estabelecimento']['atividade_principal']['descricao'],
            'state_registrations' => $response['estabelecimento']['inscricoes_estaduais'],
            'partners' => $response['socios'],
            'phone' => $response['estabelecimento']['ddd1'] . $response['estabelecimento']['telefone1'],
            'email' => $response['estabelecimento']['email'],
            'zipcode' => $response['estabelecimento']['cep'],
            'street' => $response['estabelecimento']['tipo_logradouro'] . ' ' . $response['estabelecimento']['logradouro'],
            'number' => $response['estabelecimento']['numero'],
            'complement' => $response['estabelecimento']['complemento'],
            'neighborhood' => $response['estabelecimento']['bairro'],
            'city' => $response['estabelecimento']['cidade']['nome'],
            'state' => $response['estabelecimento']['estado']['sigla'],
        ]);
    }
}
