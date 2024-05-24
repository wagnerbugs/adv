<?php

use App\Enums\GenderEnum;
use App\Enums\MaritalStatusEnum;
use App\Enums\EducationLevelEnum;
use App\Enums\TreatmentPronounEnum;
use App\Enums\TypeOfBankAccountEnum;
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
        Schema::create('client_individuals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->enum('title', TreatmentPronounEnum::getValues())->nullable();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->json('phones')->nullable();
            $table->string('email')->unique()->nullable();
            $table->json('emails')->nullable();
            $table->string('website')->nullable();
            $table->json('websites')->nullable();

            $table->enum('gender', GenderEnum::getValues())->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('marital_status', MaritalStatusEnum::getValues())->nullable();
            $table->enum('education_level', EducationLevelEnum::getValues())->nullable();
            $table->json('documents')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('nationality')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('workplace')->nullable();
            $table->string('ocupation')->nullable();
            $table->json('attachments')->nullable();
            $table->json('annotations')->nullable();
            $table->enum('type_account_bank', TypeOfBankAccountEnum::getValues())->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_agency')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('pix')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('street')->nullable();
            $table->string('number')->nullable();
            $table->string('complement')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->json('addresses')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_individuals');
    }
};
