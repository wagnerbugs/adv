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
            $table->string('code');
            $table->string('court');
            $table->string('district');
            $table->string('description');
            $table->string('type')->nullable();
            $table->string('classification')->nullable();
            $table->string('is_active')->nullable();
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
