<?php

namespace App\Services\CNJ\Procedural;

use App\Services\CNJ\Procedural\Endpoints\Classes;
use App\Services\CNJ\Procedural\Endpoints\Movements;
use App\Services\CNJ\Procedural\Endpoints\Subjects;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;

/**
 * PDPJ - Módulo de consulta às tabelas processuais unificadas
 * Serviço estruturante que provê as consulta às tabelas processuais unificadas. Versao: 1.4.0
 * Docs.: https://gateway.cloud.pje.jus.br/tpu/v2/api-docs
 */
class ProceduralService
{
    public PendingRequest $api;

    public function __construct()
    {
        $this->api = Http::baseUrl(config('services.cnj.procedural_base_url'));
    }

    /**
     * Returns a new instance of the Classes class with the current instance of ProceduralService as its parameter.
     *
     * @return Classes
     */
    public function classes(): Classes
    {
        return new Classes($this);
    }

    /**
     * Returns a new instance of the Subjects class with the current instance of ProceduralService as its parameter.
     *
     * @return Subjects
     */
    public function subjects(): Subjects
    {
        return new Subjects($this);
    }

    /**
     * Returns a new instance of the Movements class with the current instance of ProceduralService as its parameter.
     *
     * @return Movements
     */
    public function movements(): Movements
    {
        return new Movements($this);
    }
}
