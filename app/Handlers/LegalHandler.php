<?php

namespace App\Handlers;

use App\Models\ChatbotHistory;
use App\Models\ChatbotUser;

class LegalHandler extends BaseStepHandler
{
    public function handle(ChatbotHistory $history, ChatbotUser $user, ?string $message = null)
    {
        $phone = $user->phone;
        $responseMessage = "Ok {$user->name}. Por favor, informe o número do pedido:";
        $this->chatbotService->messages()->sendText($phone, $responseMessage);
    }
}
