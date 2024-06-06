<?php

namespace App\Services\CNJ\Process\Entities;

class ProcessMovement
{
    public int $movement_code;

    public string $movement_name;

    public string $movement_date;

    public ?array $complements;

    public function __construct(array $data)
    {
        $this->movement_code = data_get($data, 'codigo');
        $this->movement_name = data_get($data, 'nome');
        $this->movement_date = data_get($data, 'dataHora');
        $this->complements = data_get($data, 'complementosTabelados', []);
    }
}
