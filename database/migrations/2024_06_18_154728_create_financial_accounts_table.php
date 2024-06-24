<?php

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
        Schema::create('financial_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nome da conta
            $table->text('description')->nullable();
            $table->decimal('balance', 15, 2)->default(0); // Saldo da conta
            $table->boolean('is_active')->default(true);
            $table->boolean('is_bank')->default(false);
            $table->enum('type_account_bank', TypeOfBankAccountEnum::getValues())->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_agency')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('pix')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_accounts');
    }
};
