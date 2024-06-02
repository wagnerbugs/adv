<?php

namespace  App\Services\CNJ\Process;

use App\Services\CNJ\Process\Endpoints\Processes;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;

/**
 * PDPJ - Módulo de consulta às tabelas processuais unificadas
 * Acesso público aos metadados de processos judiciais de todo o Brasil.
 * Docs.: https://datajud-wiki.cnj.jus.br
 */
class ProcessService
{
    public PendingRequest $api;

    public function __construct()
    {
        $this->api = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'APIKey cDZHYzlZa0JadVREZDJCendQbXY6SkJlTzNjLV9TRENyQk1RdnFKZGRQdw==',
        ])->baseUrl(config('services.cnj.base_url'));
    }

    /**
     * Returns a new instance of the Processes class, passing the current instance of the ProcessService class as a parameter.
     *
     * @return Processes A new instance of the Processes class.
     */
    public function processes(): Processes
    {
        return new Processes($this);
    }
}
