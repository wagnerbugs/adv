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
        Schema::create('processes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('process')->unique();
            $table->string('process_number', 7)->nullable();
            $table->string('process_digit', 2)->nullable();
            $table->string('process_year', 4)->nullable();
            $table->string('court_code', 1)->nullable();
            $table->string('court_state_code', 2)->nullable();
            $table->string('court_district_code', 4)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processes');
    }
};
