<?php

namespace App\Handlers;

use App\Models\ChatbotHistory;
use App\Models\ChatbotUser;

class MenuHandler extends BaseStepHandler
{
    public function handle(ChatbotHistory $history, ChatbotUser $user, ?string $message = null)
    {
        $phone = $user->phone;
        $message = 'Escolha uma opção da lista:';
        $title = 'Opções Disponíveis';
        $buttonLabel = 'Mostrar Opções';
        $options = [
            [
                'id' => '2',
                'title' => 'Status do pedido',
                'description' => 'Consulte o status do seu pedido',
            ],
            [
                'id' => '3',
                'title' => 'Newsletter',
                'description' => 'Cadastrar-se na newsletter',
            ],
            [
                'id' => '9',
                'title' => 'Atendimento direto',
                'description' => 'Falar diretamente com o atendente',
            ],
        ];

        $this->chatbotService->messages()->sendOptionList($phone, $message, $title, $buttonLabel, $options);
    }
}
