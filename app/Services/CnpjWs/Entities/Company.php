<?php

namespace App\Services\CnpjWs\Entities;

use App\Traits\CapitalizeTrait;

class Company
{
    use CapitalizeTrait;

    public string $cnpj;

    public string $company;

    public string $fantasy_name;

    public string $share_capital;

    public string $company_size;

    public string $legal_nature;

    public string $type;

    public string $registration_status;

    public string $registration_date;

    public string $activity_start_date;

    public string $main_activity;

    public ?string $state_registration;

    public ?string $state_registration_location;

    public string $phone;

    public ?string $email;

    public string $zipcode;

    public string $street;

    public string $number;

    public ?string $complement;

    public string $neighborhood;

    public string $city;

    public string $state;

    public ?string $partner_name;

    public ?string $partner_type;

    public ?string $partner_qualification;

    public function __construct(array $data)
    {
        $raw_fantasy_name = data_get($data, 'estabelecimento.nome_fantasia');
        $raw_company = data_get($data, 'razao_social');

        $this->cnpj = data_get($data, 'estabelecimento.cnpj');
        $this->company = $this->capitalize(data_get($data, 'razao_social'));
        $this->fantasy_name = $this->capitalize($raw_fantasy_name ?? $raw_company);
        $this->share_capital = data_get($data, 'capital_social');
        $this->company_size = data_get($data, 'porte.descricao');
        $this->legal_nature = data_get($data, 'natureza_juridica.descricao');
        $this->type = data_get($data, 'estabelecimento.tipo');
        $this->registration_status = data_get($data, 'estabelecimento.situacao_cadastral');
        $this->registration_date = data_get($data, 'estabelecimento.data_situacao_cadastral');
        $this->activity_start_date = data_get($data, 'estabelecimento.data_inicio_atividade');
        $this->main_activity = data_get($data, 'estabelecimento.atividade_principal.descricao');

        $firstRegistration = data_get($data, 'estabelecimento.inscricoes_estaduais')[0] ?? null;
        $this->state_registration = $firstRegistration['inscricao_estadual'] ?? null;
        $this->state_registration_location = $firstRegistration['estado']['sigla'] ?? null;

        $firstPartner = data_get($data, 'socios')[0] ?? null;
        $this->partner_name = $this->capitalize($firstPartner['nome']) ?? null;
        $this->partner_type = $firstPartner['tipo'] ?? null;
        $this->partner_qualification = $firstPartner['qualificacao_socio']['descricao'] ?? null;

        $this->phone = $this->formatPhone(data_get($data, 'estabelecimento.ddd1'), data_get($data, 'estabelecimento.telefone1'));
        $this->email = data_get($data, 'estabelecimento.email');
        $this->zipcode = data_get($data, 'estabelecimento.cep');
        $this->street = $this->capitalize($this->formatStreet(data_get($data, 'estabelecimento.tipo_logradouro'), data_get($data, 'estabelecimento.logradouro')));
        $this->number = data_get($data, 'estabelecimento.numero');
        $this->complement = $this->capitalize(data_get($data, 'estabelecimento.complemento'));
        $this->neighborhood = $this->capitalize(data_get($data, 'estabelecimento.bairro'));
        $this->city = data_get($data, 'estabelecimento.cidade.nome');
        $this->state = data_get($data, 'estabelecimento.estado.sigla');
    }

    private function formatPhone(string $ddd, string $phone): string
    {
        return "($ddd) $phone";
    }

    private function formatStreet(string $type, string $street): string
    {
        return "$type $street";
    }

    public function toArray(): array
    {
        return [
            'cnpj' => $this->cnpj,
            'company' => $this->company,
            'fantasy_name' => $this->fantasy_name,
            'share_capital' => $this->share_capital,
            'company_size' => $this->company_size,
            'legal_nature' => $this->legal_nature,
            'type' => $this->type,
            'registration_status' => $this->registration_status,
            'registration_date' => $this->registration_date,
            'activity_start_date' => $this->activity_start_date,
            'main_activity' => $this->main_activity,
            'state_registration' => $this->state_registration,
            'state_registration_location' => $this->state_registration_location,
            'phone' => $this->phone,
            'email' => $this->email,
            'zipcode' => $this->zipcode,
            'street' => $this->street,
            'number' => $this->number,
            'complement' => $this->complement,
            'neighborhood' => $this->neighborhood,
            'city' => $this->city,
            'state' => $this->state,
            'partner_name' => $this->partner_name,
            'partner_type' => $this->partner_type,
            'partner_qualification' => $this->partner_qualification,
        ];
    }
}
