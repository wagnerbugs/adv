<?php

namespace App\Handlers;

use App\Models\ChatbotUser;
use App\Models\ChatbotHistory;

interface StepHandlerInterface
{
    public function handle(ChatbotHistory $history, ChatbotUser $user, string $message = null);
}
