<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ZApi\ChatbotService;
use App\Services\ZApi\Endpoints\ChatbotWithMessages;

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
        $response = $service->messages()->sendText('5548988061915', 'Finalizando o registro de vendas');

        dd($response);
    }
}
