<?php

use App\Enums\TransactionStatusEnum;
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
        Schema::create('financial_payment_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_payment_id')->constrained();
            $table->decimal('amount', 15, 2);
            $table->date('due_date');
            $table->enum('status', TransactionStatusEnum::getValues());
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_payment_installments');
    }
};
