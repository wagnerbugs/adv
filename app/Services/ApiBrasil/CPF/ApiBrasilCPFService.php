<?php

namespace App\Services\ApiBrasil\CPF;

use App\Services\ApiBrasil\CPF\Endpoints\HasIndividuals;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * APIBrasil Service
 * https://doc.apibrasil.io/
 */
class ApiBrasilCPFService
{
    use HasIndividuals;

    public PendingRequest $api;

    public function __construct()
    {
        $this->api = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.config('services.apibrasil.token'),
        ])->baseUrl(config('services.apibrasil.base_url'));
    }
}
