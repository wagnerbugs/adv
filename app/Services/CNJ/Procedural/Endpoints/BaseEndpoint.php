<?php

namespace App\Services\CNJ\Procedural\Endpoints;

use App\Services\CNJ\Procedural\ProceduralService;

class BaseEndpoint
{
    protected ProceduralService $service;

    public function __construct(ProceduralService $service)
    {
        $this->service = $service;
    }
}
