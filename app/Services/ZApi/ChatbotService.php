<?php

namespace App\Services\ZApi;

use Illuminate\Support\Facades\Http;
use App\Services\ZApi\Endpoints\Messages;
use Illuminate\Http\Client\PendingRequest;

/**
 * Class ChatbotService: Z-API - Chatbot
 * Service class to handle chatbot API interactions.
 * Docs.: https://developer.z-api.io
 */
class ChatbotService
{
    /**
     * @var PendingRequest
     */
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

    /**
     * Get the Messages endpoint instance.
     *
     * @return Messages
     */
    public function messages(): Messages
    {
        return new Messages($this);
    }
}
