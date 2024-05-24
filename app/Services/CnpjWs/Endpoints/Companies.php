<?php

namespace App\Services\CnpjWs\Endpoints;

use App\Exceptions\CnpjApiException;
use App\Services\CnpjWs\Entities\Company;
use App\Services\CnpjWs\Entities\CompanyError;
use App\Services\CnpjWs\Entities\CompanyPartner;

class Companies extends BaseEndpoint
{
    public function get(string $cnpj): Company|CompanyError
    {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        if (!$this->isValidCnpj($cnpj)) {
            return new CompanyError([
                'status' => 400,
                'title' => 'Requisição inválida',
                'details' => 'CNPJ inválido'
            ]);
        }

        try {
            $response = $this->service->api
                ->get('cnpj/' . $cnpj)
                ->json();

            if (isset($response['status']) && $response['status'] !== 200) {
                return new CompanyError($response);
            }

            return new Company($response);
        } catch (\Illuminate\Http\Client\RequestException $e) {
            return new CompanyError([
                'status' => $e->response->status(),
                'title' => 'Erro ao consultar a API',
                'details' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            return new CompanyError([
                'status' => 500,
                'title' => 'Erro interno',
                'details' => 'Erro inesperado: ' . $e->getMessage()
            ]);
        }
    }

    private function isValidCnpj(string $cnpj): bool
    {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        return strlen($cnpj) === 14 && preg_match('/^[0-9]{14}$/', $cnpj) === 1;
    }
}
