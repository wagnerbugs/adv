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
        Schema::create('court_districts', function (Blueprint $table) {
            $table->id();
            $table->string('court')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('service_number')->nullable();
            $table->string('service_name')->nullable();
            $table->string('district_code')->nullable();
            $table->string('type')->nullable();
            $table->string('unit')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->boolean('is_active')->default(true)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('court_districts');
    }
};
