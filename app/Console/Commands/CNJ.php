<?php

namespace App\Console\Commands;

use App\Services\CNJ\Process\ProcessService;
use Illuminate\Console\Command;

class CNJ extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cnj';

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
        $service = new ProcessService();
        $response = $service->processes()
            ->getProcess('api_publica_tjto', '50000046720058272711');

        dd($response);

        // $service = new ProcessService();
        // $response = $service->prospections()->getProcess('api_publica_tjto', '50000046720058272711');

        // dd($response);
    }
}
