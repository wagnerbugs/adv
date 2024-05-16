<?php

use App\Enums\GenderEnum;
use App\Enums\MaritalStatusEnum;
use App\Enums\EducationLevelEnum;
use App\Enums\EmploymentTypeEnum;
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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('gender', GenderEnum::getValues())->nullable();
            $table->date('birth_date')->nullable();
            $table->json('phone')->nullable();
            $table->string('contract')->nullable();
            $table->enum('employment_type', EmploymentTypeEnum::getValues())->nullable();
            $table->string('cbo_code')->nullable();
            $table->string('cbo_title')->nullable();
            $table->string('cbo_description')->nullable();
            $table->json('documents')->nullable();
            $table->json('attachments')->nullable();
            $table->enum('marital_status', MaritalStatusEnum::getValues())->nullable();
            $table->enum('education_level', EducationLevelEnum::getValues())->nullable();
            $table->date('hire_date')->nullable();
            $table->date('termination_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
