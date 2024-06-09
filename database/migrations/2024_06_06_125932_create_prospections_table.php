<?php

use App\Enums\ProspectionReactionEnum;
use App\Enums\ProspectionStatusEnum;
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
        Schema::create('prospections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('cnpj')->unique()->nullable();
            $table->string('cpf')->unique()->nullable();
            $table->string('process')->unique()->nullable();
            $table->json('complements')->nullable();
            $table->json('attachments')->nullable();
            $table->json('annotations')->nullable();
            $table->enum('status', ProspectionStatusEnum::getValues());
            $table->enum('reaction', ProspectionReactionEnum::getValues());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospections');
    }
};
