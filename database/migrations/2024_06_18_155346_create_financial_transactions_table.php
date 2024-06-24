<?php

use App\Enums\PaymentMethodEnum;
use App\Enums\TransactionTypeEnum;
use App\Enums\TransactionStatusEnum;
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
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('financial_account_id')->constrained('financial_accounts')->cascadeOnDelete();
            $table->foreignId('financial_category_id')->constrained('financial_categories')->cascadeOnDelete();
            $table->enum('status', TransactionStatusEnum::getValues())->nullable();
            $table->foreignId('client_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('process_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->text('note')->nullable();
            $table->enum('type', TransactionTypeEnum::getValues())->nullable();
            $table->enum('payment_method', PaymentMethodEnum::getValues())->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->foreignId('modified_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->date('transaction_date')->nullable(); //data da transção
            $table->date('due_date')->nullable(); //vencimento
            $table->date('payment_date')->nullable(); //data do pagamento
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
