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
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->string('compe', 3);  // 3 digits
            $table->string('ispb', 8);  // 8 digits
            $table->string('document', 18);  // 14 numbers formatted as 18 digits
            $table->string('long_name');  // Long name according to BACEN - STR
            $table->string('short_name');  // Short name according to BACEN - STR
            $table->string('url')->nullable();  // Website
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banks');
    }
};
