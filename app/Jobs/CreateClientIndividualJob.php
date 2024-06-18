<?php

namespace App\Jobs;

use App\Models\Client;
use App\Services\ApiBrasil\CPF\ApiBrasilCPFService;
use App\Services\ApiBrasil\CPF\Entities\Individual;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateClientIndividualJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Client $client, protected string $cpf)
    {
        //
    }

    public function handle(): void
    {
        $service = new ApiBrasilCPFService();
        $individual = $service->individuals()->get($this->cpf);

        if ($individual instanceof Individual) {
            $this->client->individual()->update([
                'name' => $individual->name,
                'gender' => $individual->gender,
                'birth_date' => $individual->birth_date,
                'mother_name' => $individual->mother_name,
            ]);
        }
    }
}
