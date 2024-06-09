<?php

use App\Enums\EducationLevelEnum;
use App\Enums\EmploymentTypeEnum;
use App\Enums\GenderEnum;
use App\Enums\MaritalStatusEnum;
use App\Enums\TypeOfBankAccountEnum;
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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('avatar')->nullable();
            $table->boolean('is_lawyer')->default(false);
            $table->enum('gender', GenderEnum::getValues())->nullable();
            $table->date('birth_date')->nullable();
            $table->json('phones')->nullable();
            $table->enum('marital_status', MaritalStatusEnum::getValues())->nullable();
            $table->enum('education_level', EducationLevelEnum::getValues())->nullable();
            $table->json('documents')->nullable();
            $table->string('contract')->nullable();
            $table->enum('employment_type', EmploymentTypeEnum::getValues())->nullable();
            $table->json('cbos')->nullable();
            $table->json('attachments')->nullable();
            $table->json('salaries')->nullable();
            $table->date('hire_date')->nullable();
            $table->date('termination_date')->nullable();
            $table->json('annotations')->nullable();
            $table->enum('type_account_bank', TypeOfBankAccountEnum::getValues())->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_agency')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('pix')->nullable();
            $table->boolean('is_active')->default(true);
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
