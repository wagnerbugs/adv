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
            $table->string('process');
            $table->string('process_number');
            $table->string('process_digit');
            $table->string('process_year');
            $table->string('court_code');
            $table->string('court_state_code');
            $table->string('court_disctric_code');
            $table->string('class_code'); //código da classe
            $table->string('class_name'); //nome da classe
            $table->text('class_description'); //descrição da classe
            $table->string('nature'); //natureza
            $table->string('active_pole'); //Polo Ativo
            $table->string('passive_pole'); //Polo Passivo
            $table->string('rule')->nullable(); //norma
            $table->string('article')->nullable(); //artigo
            $table->string('publish_date'); //data publicação
            $table->string('secrecy_level'); //nível sigilo
            $table->json('movements')->nullable();
            $table->json('subjects')->nullable();
            $table->$table->timestamps();
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
