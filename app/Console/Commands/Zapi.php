<?php

namespace App\Console\Commands;

use App\Services\ZApi\ChatbotService;
use Illuminate\Console\Command;

class Zapi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zapi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $service = new ChatbotService();

        $phone = '5548988061915';
        $message = 'Escolha uma opção da lista:';
        $title = 'Opções Disponíveis';
        $buttonLabel = 'Mostrar Opções';
        $options = [

            [
                'id' => '1',
                'title' => 'Opção 1',
                'description' => 'Descrição da opção 1',
            ],
            [
                'id' => '2',
                'title' => 'Opção 2',
                'description' => 'Descrição da opção 2',
            ],

        ];

        $response = $service->messages()->sendOptionList($phone, $message, $title, $buttonLabel, $options);

        dd($response);
    }
}
