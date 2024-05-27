<?php

namespace App\Handlers;

use App\Models\ChatbotUser;
use App\Models\ChatbotHistory;

class LegalHandler extends BaseStepHandler
{
    public function handle(ChatbotHistory $history, ChatbotUser $user, string $message = null)
    {
        $phone = $user->phone;
        $responseMessage = "Ok {$user->name}. Por favor, informe o número do pedido:";
        $this->chatbotService->messages()->sendText($phone, $responseMessage);
    }
}
