<?php

namespace App\Services\ZApi\Endpoints;

use App\Services\ZApi\ChatbotService;

class BaseEndpoint
{
    protected ChatbotService $service;

    public function __construct(ChatbotService $service)
    {
        $this->service = $service;
    }
}
