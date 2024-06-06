<?php

namespace App\Services\CNJ\Process\Entities;

class MovementComplement
{
    public int $codigo;
    public int $valor;
    public string $nome;
    public string $descricao;

    public function __construct(array $data)
    {
        $this->codigo = data_get($data, 'complementosTabelados.codigo');
        $this->valor = data_get($data, 'complementosTabelados.valor');
        $this->nome = data_get($data, 'complementosTabelados.nome');
        $this->descricao = data_get($data, 'complementosTabelados.descricao');
    }
}
