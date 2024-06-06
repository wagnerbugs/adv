<?php

namespace App\Console\Commands;

use App\Services\CnpjWs\CnpjWsService;
use Illuminate\Console\Command;

class CNPJ extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cnpj';

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
        $service = new CnpjWsService();
        $response = $service->prospections()->getCNPJ('07.386.787/0001-10');

        dd($response);
    }
}
