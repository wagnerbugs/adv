<?php

namespace App\Models;

use App\Enums\EducationLevelEnum;
use App\Enums\MaritalStatusEnum;
use App\Enums\TreatmentPronounEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProspectionIndividual extends Model
{
    use HasFactory;

    protected $fillable = [
        'prospection_id',
        'cpf',
        'mae',
        'tipo_documento',
        'nome',
        'outras_grafias',
        'data_nascimento',
        'outras_datas_nascimento',
        'pessoa_exposta_publicamente',
        'idade',
        'signo',
        'obito',
        'data_obito',
        'sexo',
        'uf',
        'situacao_receita',
        'situacao_receita_data',
        'situacao_receita_hora',
        'dados_parentes',
        'pessoas_contato',
        'pesquisa_enderecos',
        'trabalha_trabalhou',
        'contato_preferencial',
        'residentes_mesmo_domicilio',
        'emails',
        'numero_beneficio',
        'alerta_participacoes',
        'pesquisa_telefones_fixo',
        'pesquisa_telefones_celular',
        'alerta_monitore',
        'outros_documentos',
        'protocolo',
        'matriz_filial',

        'title',
        'marital_status',
        'education_level',
        'father_name',
        'nationality',
        'birth_place',
        'workplace',
        'ocupation',
    ];

    protected $casts = [
        'outras_grafias' => 'array',
        'outras_datas_nascimento' => 'array',
        'pessoa_exposta_publicamente' => 'array',
        'dados_parentes' => 'array',
        'pessoas_contato' => 'array',
        'pesquisa_enderecos' => 'array',
        'trabalha_trabalhou' => 'array',
        'contato_preferencial' => 'array',
        'residentes_mesmo_domicilio' => 'array',
        'emails' => 'array',
        'numero_beneficio' => 'array',
        'alerta_participacoes' => 'array',
        'pesquisa_telefones_fixo' => 'array',
        'pesquisa_telefones_celular' => 'array',
        'alerta_monitore' => 'array',
        'outros_documentos' => 'array',
        'matriz_filial' => 'array',

        'title' => TreatmentPronounEnum::class,
        'marital_status' => MaritalStatusEnum::class,
        'education_level' => EducationLevelEnum::class,
    ];

    public function prospection(): BelongsTo
    {
        return $this->belongsTo(Prospection::class);
    }
}
