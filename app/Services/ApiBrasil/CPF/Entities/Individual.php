<?php

namespace App\Services\ApiBrasil\CPF\Entities;

use App\Enums\GenderEnum;
use App\Traits\CapitalizeTrait;
use Carbon\Carbon;

class Individual
{
    use CapitalizeTrait;

    public ?string $cpf;

    public ?string $name;

    public ?string $birth_date;

    public ?string $mother_name;

    public GenderEnum $gender;

    public function __construct(array $data)
    {
        $this->cpf = data_get($data, 'documento');
        $this->name = $this->capitalize(data_get($data, 'nome'));
        $this->birth_date = $this->parseBirthDate(data_get($data, 'data_nascimento'));
        $this->mother_name = $this->capitalize(data_get($data, 'mae'));
        $this->gender = GenderEnum::parse(data_get($data, 'sexo'));
    }

    private function parseBirthDate(?string $date): ?string
    {
        if ($date) {
            try {
                return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }

    public function toArray(): array
    {
        return [
            'cpf' => $this->cpf,
            'name' => $this->name,
            'birth_date' => $this->birth_date,
            'gender' => $this->gender,
            'mother_name' => $this->mother_name,
        ];
    }
}
