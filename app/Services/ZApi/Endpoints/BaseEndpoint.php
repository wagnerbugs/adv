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
    /**
     * @var ChatbotService
     */
    protected ChatbotService $service;

    /**
     * BaseEndpoint constructor.
     *
     * @param ChatbotService $service
     */
    public function __construct(ChatbotService $service)
    {
        $this->service = $service;
    }
}
