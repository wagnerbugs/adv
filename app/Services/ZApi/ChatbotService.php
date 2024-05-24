<?php

namespace App\Services\ZApi;

use App\Services\ZApi\Endpoints\CanSendMessages;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * Class ChatbotService: Z-API - Chatbot
 * Service class to handle chatbot API interactions.
 * Docs.: https://developer.z-api.io
 */
class ChatbotService
{
    use CanSendMessages;

    public PendingRequest $api;

    /**
     * ChatbotService constructor.
     */
    public function __construct()
    {
        $this->api = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Client-Token' => config('services.zapi.token_secure'),
        ])->baseUrl(config('services.zapi.base_url'));
    }
}
