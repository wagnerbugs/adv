<?php

namespace App\Console\Commands;

use App\Services\ApiBrasil\CPF\ApiBrasilCPFService;
use Illuminate\Console\Command;

class CPF extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cpf';

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
        $service = new ApiBrasilCPFService();
        $response = $service->prospections()->getCPF('80538533072');

        dd($response);
    }
}
