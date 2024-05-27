<?php

namespace App\Services\ZApi\Endpoints;

use App\Services\ZApi\ChatbotService;

/**
 * Class BaseEndpoint
 *
 * Base class for all API endpoints.
 */
class BaseEndpoint
{
    protected ChatbotService $service;

    /**
     * BaseEndpoint constructor.
     */
    public function __construct(ChatbotService $service)
    {
        $this->service = $service;
    }
}
