<?php

namespace App\Services\ApiBrasil\CPF\Endpoints;

use App\Services\ApiBrasil\CPF\ApiBrasilCPFService;

class BaseEndpoint
{
    protected ApiBrasilCPFService $service;

    public function __construct()
    {
        $this->service = new ApiBrasilCPFService();
    }
}
