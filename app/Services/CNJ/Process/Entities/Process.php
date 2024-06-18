<?php

namespace App\Services\CNJ\Process\Entities;

class Process
{
    public string $process_api_id; //ID do processo

    public string $class_code; //Código da classe

    public string $class_name; //Nome da classe

    public string $numeroProcesso; //Número do processo

    public string $sistema_codigo; //Código do sistema

    public string $sistema_nome;

    public string $formato_codigo;

    public string $formato_nome;

    public string $tribunal;

    public string $last_modification_date; //Data da última alteração

    public string $grade; //Grau

    public string $data_consulta; //Data da consulta

    public string $publish_date; //Data de publicação

    public ?array $movements; //Movimentos

    public int $secrecy_level; //Nível de sigilo

    public string $orgaoJulgador_codigoMunicipioIBGE; //Código do município

    public string $judging_code; //Código do órgão Julgador

    public string $judging_name;

    public ?array $subjects;

    public function __construct(array $data)
    {
        $this->process_api_id = data_get($data, '_id');
        $this->class_code = data_get($data, '_source.classe.codigo');
        $this->class_name = data_get($data, '_source.classe.nome');
        $this->numeroProcesso = data_get($data, '_source.numeroProcesso');
        $this->sistema_codigo = data_get($data, '_source.sistema.codigo');
        $this->sistema_nome = data_get($data, '_source.sistema.nome');
        $this->formato_codigo = data_get($data, '_source.formato.codigo');
        $this->formato_nome = data_get($data, '_source.formato.nome');
        $this->tribunal = data_get($data, '_source.tribunal');
        $this->last_modification_date = data_get($data, '_source.dataHoraUltimaAtualizacao');
        $this->grade = data_get($data, '_source.grau');
        $this->data_consulta = data_get($data, '_source.@timestamp');
        $this->publish_date = data_get($data, '_source.dataAjuizamento');
        // $this->movements = data_get($data, '_source.movimentos', []);
        $this->movements = array_map(fn ($movement) => new ProcessMovement($movement), data_get($data, '_source.movimentos', []));
        $this->secrecy_level = data_get($data, '_source.nivelSigilo');
        $this->orgaoJulgador_codigoMunicipioIBGE = data_get($data, '_source.orgaoJulgador.codigoMunicipioIBGE');
        $this->judging_code = data_get($data, '_source.orgaoJulgador.codigo');
        $this->judging_name = data_get($data, '_source.orgaoJulgador.nome');
        $this->subjects = array_map(fn ($subject) => new ProcessSubject($subject), data_get($data, '_source.assuntos', []));
    }
}
