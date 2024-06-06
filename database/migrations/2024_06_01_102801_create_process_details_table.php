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
        Schema::create('process_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('process_id')->constrained()->cascadeOnDelete();
            $table->string('process_api_id')->unique()->nullable();
            $table->string('class_code')->nullable(); //código da classe
            $table->string('class_name')->nullable(); //nome da classe
            $table->text('class_description')->nullable(); //descrição da classe
            $table->string('nature')->nullable(); //natureza
            $table->string('active_pole')->nullable(); //Polo Ativo
            $table->string('passive_pole')->nullable(); //Polo Passivo
            $table->string('rule')->nullable(); //norma
            $table->string('article')->nullable(); //artigo
            $table->dateTime('last_modification_date')->nullable(); //data da última alteração
            $table->string('grade')->nullable();
            $table->dateTime('publish_date')->nullable(); //data publicação
            $table->json('movements')->nullable();
            $table->string('secrecy_level')->nullable(); //nível sigilo
            $table->string('judging_code')->nullable();
            $table->string('judging_name')->nullable();
            $table->json('subjects')->nullable();
            $table->json('attachments')->nullable();
            $table->json('annotations')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('process_details');
    }
};
