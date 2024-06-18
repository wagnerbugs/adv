<?php

namespace App\Services\CnpjWs\Endpoints;

class Prospections extends BaseEndpoint
{
    public function getCNPJ(string $cnpj): array
    {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        return $this->service->api
            ->get('cnpj/'.$cnpj)
            ->json();
    }
}
