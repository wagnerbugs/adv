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
        Schema::create('prospection_processes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospection_id')->constrained()->cascadeOnDelete();
            $table->string('process')->nullable();
            $table->string('process_number', 7)->nullable();
            $table->string('process_digit', 2)->nullable();
            $table->string('process_year', 4)->nullable();
            $table->string('court_code', 1)->nullable();
            $table->string('court_state_code', 2)->nullable();
            $table->string('court_district_code', 4)->nullable();
            $table->json('classe')->nullable();
            $table->json('sistema')->nullable();
            $table->json('formato')->nullable();
            $table->string('tribunal')->nullable();
            $table->dateTime('dataHoraUltimaAtualizacao')->nullable();
            $table->string('grau')->nullable();
            $table->dateTime('dataAjuizamento')->nullable();
            $table->json('movimentos')->nullable();
            $table->string('process_api_id')->nullable();
            $table->integer('nivelSigilo')->nullable();
            $table->json('orgaoJulgador')->nullable();
            $table->json('assuntos')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospection_processes');
    }
};
