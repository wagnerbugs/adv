<?php

namespace App\Services\CnpjWs;

use App\Services\CnpjWs\Endpoints\HasCompanies;
use App\Services\CnpjWs\Endpoints\HasProspections;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * CNPJ.WS Web Service
 * https://www.cnpj.ws/docs/api-publica/consultando-cnpj
 */
class CnpjWsService
{
    use HasCompanies, HasProspections;

    public PendingRequest $api;

    public function __construct()
    {
        $this->api = Http::baseUrl(config('services.cnpj_ws.base_url'));
    }
}
