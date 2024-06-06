<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProspectionCompany extends Model
{
    use HasFactory;

    protected $fillable = [
        'prospection_id',
        'cnpj',
        'cnpj_raiz',
        'cnpj_ordem',
        'cnpj_digito_verificador',
        'tipo',
        'nome_fantasia',
        'motivo_situacao_cadastral',
        'data_situacao_cadastral',
        'data_inicio_atividade',
        'nome_cidade_exterior',
        'tipo_logradouro',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cep',
        'ddd1',
        'telefone1',
        'ddd2',
        'telefone2',
        'ddd_fax',
        'fax',
        'email',
        'situacao_especial',
        'data_situacao_especial',
        'atualizado_em',
        'atividade_principal',
        'pais',
        'estado',
        'cidade',
        'inscricoes_estaduais',
        'razao_social',
        'capital_social',
        'responsavel_federativo',
        'atualizado_em',
        'porte',
        'natureza_juridica',
        'qualificacao_do_responsavel',
        'socios',
        'simples',
        'atividades_secundarias',
    ];

    protected $casts = [
        'atividade_principal' => 'array',
        'pais' => 'array',
        'estado' => 'array',
        'cidade' => 'array',
        'inscricoes_estaduais' => 'array',
        'porte' => 'array',
        'natureza_juridica' => 'array',
        'qualificacao_do_responsavel' => 'array',
        'atividades_secundarias' => 'array',
    ];
}
