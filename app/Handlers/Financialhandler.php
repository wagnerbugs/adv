<?php

namespace App\Handlers;

use App\Models\ChatbotUser;
use App\Models\ChatbotHistory;

class FinancialHandler extends BaseStepHandler
{
    public function handle(ChatbotHistory $history, ChatbotUser $user, string $message = null)
    {
        $phone = $user->phone;
        $responseMessage = "Você escolheu se cadastrar na newsletter. Por favor, informe seu email:";
        $this->chatbotService->messages()->sendText($phone, $responseMessage);
    }
}
