<?php

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
        Schema::create('client_companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('company')->nullable();
            $table->string('fantasy_name')->nullable();
            $table->string('share_capital')->nullable();
            $table->string('company_size')->nullable();
            $table->string('legal_nature')->nullable();
            $table->string('type')->nullable(); //Matriz ou Filial
            $table->string('registration_status')->nullable();
            $table->date('registration_date')->nullable();
            $table->date('activity_start_date')->nullable();
            $table->string('main_activity')->nullable();
            $table->json('secondary_activities')->nullable();
            $table->json('state_registrations')->nullable();
            $table->json('partners')->nullable();
            $table->string('phone')->nullable();
            $table->json('phones')->nullable();
            $table->string('email')->nullable();
            $table->json('emails')->nullable();
            $table->string('website')->nullable();
            $table->json('websites')->nullable();
            $table->json('contacts')->nullable();
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
        Schema::dropIfExists('client_companies');
    }
};
