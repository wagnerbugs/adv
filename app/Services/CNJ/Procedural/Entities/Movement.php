<?php

namespace App\Services\CNJ\Procedural\Entities;

class Movement
{
    public $codigo;

    public $descricao;

    public function __construct(array $data)
    {
        $this->codigo = data_get($data, 'codigo');
        $this->descricao = data_get($data, 'glossario');
    }
}
