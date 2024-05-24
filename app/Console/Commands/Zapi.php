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
    public function handle(ChatbotService $service)
    {
        $response = $service->messages()->sendText('5548988061915', 'test');

        dd($response);
    }
}
