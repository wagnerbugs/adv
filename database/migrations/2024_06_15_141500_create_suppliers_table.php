<?php

use App\Enums\ClientTypeEnum;
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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ClientTypeEnum::getValues());
            $table->string('document');
            $table->string('name');
            $table->string('phone')->nullable();
            $table->json('phones')->nullable();
            $table->string('email')->nullable();
            $table->json('emails')->nullable();
            $table->string('website')->nullable();
            $table->json('websites')->nullable();
            $table->json('contacts')->nullable();
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
        Schema::dropIfExists('suppliers');
    }
};
