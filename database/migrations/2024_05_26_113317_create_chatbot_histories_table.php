<?php

use App\Enums\ChatbotStepsEnum;
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
        Schema::create('chatbot_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chatbot_user_id')->constrained()->cascadeOnDelete();
            $table->enum('step', ChatbotStepsEnum::getValues())->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_histories');
    }
};
