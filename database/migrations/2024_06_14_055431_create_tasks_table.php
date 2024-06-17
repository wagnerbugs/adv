<?php

use App\Enums\TaskPriorityEnum;
use App\Enums\TaskStatusesEnum;
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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('color')->nullable();
            $table->json('professionals')->nullable();
            $table->foreignId('process_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->enum('priority', [TaskPriorityEnum::getValues()]);
            $table->string('order_column');
            $table->boolean('is_private')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_urgent')->default(false);
            $table->enum('status', TaskStatusesEnum::getValues());
            $table->dateTime('deadline_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
