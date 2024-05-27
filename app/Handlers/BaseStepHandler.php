<?php

namespace App\Handlers;

use App\Models\ChatbotUser;
use App\Models\ChatbotHistory;
use App\Services\ZApi\ChatbotService;

abstract class BaseStepHandler implements StepHandlerInterface
{
    protected ChatbotService $chatbotService;

    public function __construct(ChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    abstract public function handle(ChatbotHistory $history, ChatbotUser $user, string $message = null);
}
