<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use SoapClient;

class Hash extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hash';

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
        // $processNumber = $this->argument('processNumber');
        $processNumber = '08791258920208140301';

        $wsdl = env('SOAP_WSDL_URL');

        $client = new SoapClient($wsdl, [
            'cache_wsdl' => WSDL_CACHE_NONE,
            'trace' => true,
        ]);

        try {
            $response = $client->__soapCall('dadosProcessoCNJ', [
                'numeroProcessoCNJ' => $processNumber,
                'tipoConsulta' => 'PRIMEIRO_GRAU',
            ]);

            dd($response);

            if (isset($response->return->status->resultado) && $response->return->status->resultado === 'SUCESSO') {
                $this->info('Process data fetched successfully.');
                $this->line(print_r($response, true));
            } else {
                $errorMessage = $response->return->status->mensagem ?? 'Erro desconhecido ao processar o processo.';
                $this->error('Error fetching process data: ' . $errorMessage);
            }
        } catch (\Exception $e) {
            $this->error('Error fetching process data: ' . $e->getMessage());
        }

        return 0;
    }
}
