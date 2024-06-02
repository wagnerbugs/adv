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
        Schema::create('processes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('process')->unique();
            $table->string('process_number', 7)->nullable();
            $table->string('process_digit', 2)->nullable();
            $table->string('process_year', 4)->nullable();
            $table->string('court_code', 1)->nullable();
            $table->string('court_state_code', 2)->nullable();
            $table->string('court_disctric_code', 4)->nullable();
            $table->string('class_code')->nullable(); //código da classe
            $table->string('class_name')->nullable(); //nome da classe
            $table->text('class_description')->nullable(); //descrição da classe
            $table->string('nature')->nullable(); //natureza
            $table->string('active_pole')->nullable(); //Polo Ativo
            $table->string('passive_pole')->nullable(); //Polo Passivo
            $table->string('rule')->nullable(); //norma
            $table->string('article')->nullable(); //artigo
            $table->dateTime('publish_date')->nullable(); //data publicação
            $table->dateTime('last_modification_date')->nullable(); //data da última alteração
            $table->string('secrecy_level')->nullable(); //nível sigilo
            $table->json('movements')->nullable();
            $table->json('subjects')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processes');
    }
};
