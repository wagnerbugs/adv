<?php

namespace App\Services\ApiBrasil\CPF\Endpoints;

use App\Exceptions\ApiBrasilErrorResponseException;
use App\Exceptions\ApiBrasilRequestException;
use App\Exceptions\InvalidCpfException;
use App\Services\ApiBrasil\CPF\Entities\Individual;

class Individuals extends BaseEndpoint
{
    public function get(string $cpf)
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        $response = $this->service->api
            ->post('cpf/credits', ['cpf' => $cpf])
            ->json();

        return new Individual($response['response']['content']['nome']['conteudo']);
    }
}
