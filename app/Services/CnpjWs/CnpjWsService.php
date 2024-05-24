<?php

namespace App\Services\CnpjWs;

use App\Services\CnpjWs\Endpoints\HasCompanies;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;

/**
 * CNPJ.WS Web Service
 * https://www.cnpj.ws/docs/api-publica/consultando-cnpj
 */
class CnpjWsService
{
    use HasCompanies;

    public PendingRequest $api;

    public function __construct()
    {
        $this->api = Http::baseUrl(config('services.cnpj_ws.base_url'));
    }
}
