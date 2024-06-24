<?php

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
        Schema::create('financial_transaction_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('financial_transaction_id'); // ID da transação
            $table->string('file_path'); // Caminho do arquivo
            $table->timestamps();

            $table->foreign('financial_transaction_id')->references('id')->on('financial_transactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('financial_transaction_files', function (Blueprint $table) {
            $table->dropForeign(['financial_transaction_id']); // Remove a chave estrangeira
        });
        Schema::dropIfExists('financial_transaction_files');
    }
};
