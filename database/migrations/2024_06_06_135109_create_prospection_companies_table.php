<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prospection_companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospection_id')->constrained()->cascadeOnDelete();
            $table->string('cnpj')->nullable();
            $table->string('cnpj_raiz')->nullable();
            $table->string('cnpj_ordem')->nullable();
            $table->string('cnpj_digito_verificador')->nullable();
            $table->string('tipo')->nullable();
            $table->string('nome_fantasia')->nullable();
            $table->string('motivo_situacao_cadastral')->nullable();
            $table->date('data_situacao_cadastral')->nullable();
            $table->date('data_inicio_atividade')->nullable();
            $table->string('nome_cidade_exterior')->nullable();
            $table->string('tipo_logradouro')->nullable();
            $table->string('logradouro')->nullable();
            $table->string('numero')->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cep')->nullable();
            $table->string('ddd1')->nullable();
            $table->string('telefone1')->nullable();
            $table->string('ddd2')->nullable();
            $table->string('telefone2')->nullable();
            $table->string('ddd_fax')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->nullable();
            $table->string('situacao_especial')->nullable();
            $table->date('data_situacao_especial')->nullable();
            $table->date('atualizado_em')->nullable();
            $table->json('atividade_principal')->nullable();
            $table->json('pais')->nullable();
            $table->json('estado')->nullable();
            $table->json('cidade')->nullable();
            $table->json('inscricoes_estaduais')->nullable();
            $table->string('razao_social')->nullable();
            $table->string('capital_social')->nullable();
            $table->string('responsavel_federativo')->nullable();
            $table->json('porte')->nullable();
            $table->json('natureza_juridica')->nullable();
            $table->json('qualificacao_do_responsavel')->nullable();
            $table->json('socios')->nullable();
            $table->string('simples')->nullable();
            $table->json('atividades_secundarias')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospection_companies');
    }
};
