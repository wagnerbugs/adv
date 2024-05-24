<?php

namespace App\Services\ApiBrasil\CPF\Endpoints;

use App\Exceptions\InvalidCpfException;
use App\Exceptions\ApiBrasilRequestException;
use App\Exceptions\ApiBrasilErrorResponseException;
use App\Services\ApiBrasil\CPF\Entities\Individual;
use Illuminate\Support\Facades\Log;

class Individuals extends BaseEndpoint
{
    public function get(string $cpf)
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        if (!$this->isValidCpf($cpf)) {
            throw new InvalidCpfException();
        }

        try {
            $response = $this->service->api
                ->post('cpf/credits', ['cpf' => $cpf])
                ->json();

            if (isset($response['error']) && $response['error'] === true) {
                $errorMessage = $response['message'] ?? 'API returned an error';
                throw new ApiBrasilErrorResponseException(
                    $errorMessage,
                    $response
                );
            }

            return new Individual($response['response']['content']['nome']['conteudo']);
        } catch (\Illuminate\Http\Client\RequestException $e) {
            throw new ApiBrasilRequestException($e->getMessage(), $e->getCode(), $e);
        } catch (\Exception $e) {
            throw new ApiBrasilRequestException($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function isValidCpf(string $cpf): bool
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        if (strlen($cpf) !== 11 || preg_match('/^[0-9]{11}$/', $cpf) !== 1) {
            return false;
        }

        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        return true;
    }
}
