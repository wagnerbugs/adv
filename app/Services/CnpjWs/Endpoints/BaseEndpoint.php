<?php

namespace App\Services\CnpjWs\Endpoints;

use App\Services\CnpjWs\CnpjWsService;

class BaseEndpoint
{
    protected CnpjWsService $service;

    public function __construct()
    {
        $this->service = new CnpjWsService();
    }
}
