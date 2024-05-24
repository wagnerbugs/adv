<?php

namespace App\Services\ZApi\Endpoints;

use App\Services\ZApi\ChatbotService;

class BaseEndpoint
{
    public function __construct(protected ChatbotService $service)
    {
    }
}
