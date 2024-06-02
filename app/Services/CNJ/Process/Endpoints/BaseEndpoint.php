<?php

namespace App\Services\CNJ\Process\Endpoints;

use App\Services\CNJ\Process\ProcessService;

class BaseEndpoint
{
    protected ProcessService $service;

    public function __construct(ProcessService $service)
    {
        $this->service = $service;
    }
}
