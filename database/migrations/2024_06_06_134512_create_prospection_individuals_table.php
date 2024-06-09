<?php

use App\Enums\MaritalStatusEnum;
use App\Enums\EducationLevelEnum;
use App\Enums\TreatmentPronounEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prospection_individuals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospection_id')->constrained()->cascadeOnDelete();
            $table->string('cpf')->nullable();
            $table->string('mae')->nullable();
            $table->string('tipo_documento')->nullable();
            $table->string('nome')->nullable();
            $table->json('outras_grafias')->nullable();
            $table->string('data_nascimento')->nullable();
            $table->json('outras_datas_nascimento')->nullable();
            $table->json('pessoa_exposta_publicamente')->nullable();
            $table->string('idade')->nullable();
            $table->string('signo')->nullable();
            $table->string('obito')->nullable();
            $table->string('data_obito')->nullable();
            $table->string('sexo')->nullable();
            $table->string('uf')->nullable();
            $table->string('situacao_receita')->nullable();
            $table->string('situacao_receita_data')->nullable();
            $table->string('situacao_receita_hora')->nullable();
            $table->json('dados_parentes')->nullable();
            $table->json('pessoas_contato')->nullable();
            $table->json('pesquisa_enderecos')->nullable();
            $table->json('trabalha_trabalhou')->nullable();
            $table->json('contato_preferencial')->nullable();
            $table->json('residentes_mesmo_domicilio')->nullable();
            $table->json('emails')->nullable();
            $table->json('numero_beneficio')->nullable();
            $table->json('alerta_participacoes')->nullable();
            $table->json('pesquisa_telefones_fixo')->nullable();
            $table->json('pesquisa_telefones_celular')->nullable();
            $table->json('alerta_monitore')->nullable();
            $table->json('outros_documentos')->nullable();
            $table->string('protocolo')->nullable();

            $table->enum('title', TreatmentPronounEnum::getValues())->nullable();
            $table->enum('marital_status', MaritalStatusEnum::getValues())->nullable();
            $table->enum('education_level', EducationLevelEnum::getValues())->nullable();
            $table->string('father_name')->nullable();
            $table->string('nationality')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('workplace')->nullable();
            $table->string('ocupation')->nullable();

            $table->json('matriz_filial')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospection_individuals');
    }
};
