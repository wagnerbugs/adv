<?php

namespace App\Handlers;

use App\Models\ChatbotHistory;
use App\Models\ChatbotUser;

interface StepHandlerInterface
{
    public function handle(ChatbotHistory $history, ChatbotUser $user, ?string $message = null);
}
