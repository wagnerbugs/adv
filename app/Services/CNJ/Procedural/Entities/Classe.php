<?php

namespace App\Services\CNJ\Procedural\Entities;

class Classe
{
    public string $class_description;
    public ?string $nature;
    public ?string $active_pole;
    public ?string $passive_pole;
    public ?string $rule;
    public ?string $article;

    public function __construct(array $data)
    {
        $this->class_description = data_get($data, 'descricao_glossario');
        $this->nature = data_get($data, 'natureza');
        $this->active_pole = data_get($data, 'pole_ativo');
        $this->passive_pole = data_get($data, 'pole_passivo');
        $this->rule = data_get($data, 'norma');
        $this->article = data_get($data, 'artigo');
    }
}
